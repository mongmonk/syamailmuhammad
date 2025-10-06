<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Chapter;
use App\Models\Hadith;
use App\Models\UserReadingProgress;

class ProgressControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $status, string $role = User::ROLE_USER): User
    {
        return User::factory()->create([
            'status' => $status,
            'role' => $role,
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_index_json_for_pending_user_initially_empty(): void
    {
        $user = $this->makeUser(User::STATUS_PENDING);
        $this->actingAs($user);

        $resp = $this->getJson(route('progress.index'));
        $resp->assertStatus(200);
        $resp->assertJson([
            'data' => [],
        ]);
    }

    public function test_index_web_view_renders_for_pending_user(): void
    {
        $user = $this->makeUser(User::STATUS_PENDING);
        $this->actingAs($user);

        $resp = $this->get(route('progress.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Progres Baca');
    }

    public function test_store_upserts_chapter_progress_and_returns_payload_json(): void
    {
        $user = $this->makeUser(User::STATUS_PENDING);
        $this->actingAs($user);

        $chapter = Chapter::factory()->create();

        // Bypass CSRF untuk pengujian POST web route
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        // Create
        $r1 = $this->postJson(route('progress.store'), [
            'type' => UserReadingProgress::TYPE_CHAPTER,
            'resource_id' => $chapter->id,
            'position' => 5,
        ]);
        $r1->assertStatus(200);
        $r1->assertJsonPath('message', 'Progres diperbarui');
        $r1->assertJsonPath('data.type', UserReadingProgress::TYPE_CHAPTER);
        $r1->assertJsonPath('data.resource_id', $chapter->id);
        $r1->assertJsonPath('data.position', 5);

        $this->assertDatabaseHas('user_reading_progress', [
            'user_id' => $user->id,
            'resource_type' => UserReadingProgress::TYPE_CHAPTER,
            'resource_id' => $chapter->id,
            'position' => 5,
        ]);

        // Update (upsert)
        $r2 = $this->postJson(route('progress.store'), [
            'type' => UserReadingProgress::TYPE_CHAPTER,
            'resource_id' => $chapter->id,
            'position' => 10,
        ]);
        $r2->assertStatus(200);
        $r2->assertJsonPath('data.position', 10);

        $this->assertDatabaseHas('user_reading_progress', [
            'user_id' => $user->id,
            'resource_type' => UserReadingProgress::TYPE_CHAPTER,
            'resource_id' => $chapter->id,
            'position' => 10,
        ]);
    }

    public function test_store_returns_422_when_resource_not_found(): void
    {
        $user = $this->makeUser(User::STATUS_PENDING);
        $this->actingAs($user);

        // Bypass CSRF untuk pengujian POST web route
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        $resp = $this->postJson(route('progress.store'), [
            'type' => UserReadingProgress::TYPE_HADITH,
            'resource_id' => 999999,
            'position' => 1,
        ]);

        $resp->assertStatus(422);
        $resp->assertJsonStructure(['message']);
    }

    public function test_store_accepts_hadith_progress_when_exists(): void
    {
        $user = $this->makeUser(User::STATUS_PENDING);
        $this->actingAs($user);

        $chapter = Chapter::factory()->create();
        $hadith = Hadith::factory()->create([
            'chapter_id' => $chapter->id,
        ]);

        // Bypass CSRF untuk pengujian POST web route
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        $resp = $this->postJson(route('progress.store'), [
            'type' => UserReadingProgress::TYPE_HADITH,
            'resource_id' => $hadith->id,
            'position' => 3,
        ]);

        $resp->assertStatus(200);
        $resp->assertJsonPath('data.type', UserReadingProgress::TYPE_HADITH);
        $resp->assertJsonPath('data.resource_id', $hadith->id);
        $resp->assertJsonPath('data.position', 3);

        $this->assertDatabaseHas('user_reading_progress', [
            'user_id' => $user->id,
            'resource_type' => UserReadingProgress::TYPE_HADITH,
            'resource_id' => $hadith->id,
            'position' => 3,
        ]);
    }
}