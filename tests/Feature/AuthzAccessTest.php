<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Chapter;
use App\Models\Hadith;

class AuthzAccessTest extends TestCase
{
    use RefreshDatabase;

    private Chapter $chapter;
    private Hadith $hadith;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed minimal data: 1 Chapter + 1 Hadith dengan tafsir berisi footnote
        $this->chapter = Chapter::factory()->create([
            'title' => 'Bab 1',
            'description' => 'Deskripsi Bab 1',
            'chapter_number' => 1,
        ]);

        $this->hadith = Hadith::factory()->create([
            'chapter_id' => $this->chapter->id,
            'hadith_number' => 1,
            'arabic_text' => 'النص العربي',
            'translation' => 'Terjemahan contoh',
            // Tafsir dengan footnote penanda [[...]] dan ((...))
            'interpretation' => 'Tafsir sample dengan footnote [[Catatan satu]] dan tambahan ((Catatan dua)).',
            'narration_source' => 'Sumber Riwayat',
        ]);
    }

    private function makeUser(string $status, string $role = User::ROLE_USER): User
    {
        return User::factory()->create([
            'status' => $status,
            'role' => $role,
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_hadith_requires_auth_for_json_requests_returns_401_when_not_logged_in()
    {
        $resp = $this->getJson(route('hadiths.show', ['hadith' => $this->hadith->id]));
        $resp->assertStatus(401);
        $resp->assertJson([
            'code' => 'UNAUTHENTICATED',
        ]);
    }

    public function test_hadith_returns_403_for_pending_user_json()
    {
        $user = $this->makeUser(User::STATUS_PENDING);
        $this->actingAs($user);

        $resp = $this->getJson(route('hadiths.show', ['hadith' => $this->hadith->id]));
        $resp->assertStatus(403);
        $resp->assertJson([
            'code' => 'USER_STATUS_NOT_ACTIVE',
        ]);
    }

    public function test_hadith_returns_403_for_banned_user_json()
    {
        $user = $this->makeUser(User::STATUS_BANNED);
        $this->actingAs($user);

        $resp = $this->getJson(route('hadiths.show', ['hadith' => $this->hadith->id]));
        $resp->assertStatus(403);
        $resp->assertJson([
            'code' => 'USER_STATUS_BANNED',
        ]);
    }

    public function test_hadith_returns_200_for_active_user_and_includes_footnotes_in_json()
    {
        $user = $this->makeUser(User::STATUS_ACTIVE);
        $this->actingAs($user);

        $resp = $this->getJson(route('hadiths.show', ['hadith' => $this->hadith->id]));
        $resp->assertStatus(200);
        $resp->assertJsonStructure([
            'hadith' => [
                'id',
                'chapter_id',
                'hadith_number',
                'arabic_text',
                'translation',
                'interpretation',
                'footnotes',
            ],
            'previousHadith',
            'nextHadith',
        ]);

        $data = $resp->json();
        $this->assertIsArray($data['hadith']['footnotes']);
        $this->assertCount(2, $data['hadith']['footnotes']);
        $this->assertSame(1, $data['hadith']['footnotes'][0]['index']);
        $this->assertSame(2, $data['hadith']['footnotes'][1]['index']);
        $this->assertStringContainsString('Catatan satu', $data['hadith']['footnotes'][0]['content']);
        $this->assertStringContainsString('Catatan dua', $data['hadith']['footnotes'][1]['content']);
    }

    public function test_chapter_requires_auth_active_with_json_and_includes_footnotes_payload_for_active()
    {
        // Not logged in => 401
        $resp401 = $this->getJson(route('chapters.show', ['chapter' => $this->chapter->id]));
        $resp401->assertStatus(401);
        $resp401->assertJson(['code' => 'UNAUTHENTICATED']);

        // Pending => 403
        $pending = $this->makeUser(User::STATUS_PENDING);
        $this->actingAs($pending);
        $resp403Pending = $this->getJson(route('chapters.show', ['chapter' => $this->chapter->id]));
        $resp403Pending->assertStatus(403);
        $resp403Pending->assertJson(['code' => 'USER_STATUS_NOT_ACTIVE']);

        // Banned => 403
        $banned = $this->makeUser(User::STATUS_BANNED);
        $this->actingAs($banned);
        $resp403Banned = $this->getJson(route('chapters.show', ['chapter' => $this->chapter->id]));
        $resp403Banned->assertStatus(403);
        $resp403Banned->assertJson(['code' => 'USER_STATUS_BANNED']);

        // Active => 200 + payload footnotes untuk hadiths
        $active = $this->makeUser(User::STATUS_ACTIVE);
        $this->actingAs($active);
        $resp200 = $this->getJson(route('chapters.show', ['chapter' => $this->chapter->id]));
        $resp200->assertStatus(200);
        $resp200->assertJsonStructure([
            'chapter' => ['id', 'chapter_number', 'title', 'description'],
            'hadiths' => [
                ['id', 'hadith_number', 'arabic_text', 'translation', 'interpretation', 'footnotes']
            ],
        ]);

        $payload = $resp200->json();
        $this->assertIsArray($payload['hadiths'][0]['footnotes']);
        $this->assertCount(2, $payload['hadiths'][0]['footnotes']);
    }

    public function test_search_form_requires_active_status_json()
    {
        // Not logged in => 401
        $resp401 = $this->getJson(route('search.form'));
        $resp401->assertStatus(401);
        $resp401->assertJson(['code' => 'UNAUTHENTICATED']);

        // Pending => 403
        $pending = $this->makeUser(User::STATUS_PENDING);
        $this->actingAs($pending);
        $resp403Pending = $this->getJson(route('search.form'));
        $resp403Pending->assertStatus(403);
        $resp403Pending->assertJson(['code' => 'USER_STATUS_NOT_ACTIVE']);

        // Banned => 403
        $banned = $this->makeUser(User::STATUS_BANNED);
        $this->actingAs($banned);
        $resp403Banned = $this->getJson(route('search.form'));
        $resp403Banned->assertStatus(403);
        $resp403Banned->assertJson(['code' => 'USER_STATUS_BANNED']);

        // Active => 200
        $active = $this->makeUser(User::STATUS_ACTIVE);
        $this->actingAs($active);
        $resp200 = $this->get(route('search.form')); // web response oke (non-JSON) untuk render form
        $resp200->assertStatus(200);
    }

    public function test_profile_accessible_for_pending_and_active_but_forbidden_for_banned()
    {
        // Pending => 200
        $pending = $this->makeUser(User::STATUS_PENDING);
        $this->actingAs($pending);
        $r1 = $this->get(route('profile.show'));
        $r1->assertStatus(200);

        // Active => 200
        $active = $this->makeUser(User::STATUS_ACTIVE);
        $this->actingAs($active);
        $r2 = $this->get(route('profile.show'));
        $r2->assertStatus(200);

        // Banned => 403 (not.banned middleware)
        $banned = $this->makeUser(User::STATUS_BANNED);
        $this->actingAs($banned);
        $r3 = $this->getJson(route('profile.show')); // gunakan JSON agar 403 dengan payload {code, message}
        $r3->assertStatus(403);
        $r3->assertJson(['code' => 'USER_STATUS_BANNED']);
    }
}