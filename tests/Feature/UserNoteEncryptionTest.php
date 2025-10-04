<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Chapter;
use App\Models\Hadith;
use App\Models\UserNote;

class UserNoteEncryptionTest extends TestCase
{
    use RefreshDatabase;

    protected function createVerifiedUser(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        return $user;
    }

    protected function createHadith(): Hadith
    {
        $chapter = Chapter::factory()->create([
            'chapter_number' => 1,
        ]);

        return Hadith::factory()->create([
            'chapter_id' => $chapter->id,
            'hadith_number' => 1,
            'arabic_text' => 'النص العربي للاختبار',
            'translation' => 'Terjemahan untuk pengujian',
            'narration_source' => 'Sumber Riwayat',
        ]);
    }

    public function test_note_content_is_encrypted_at_rest(): void
    {
        $user = $this->createVerifiedUser();
        $hadith = $this->createHadith();

        $this->actingAs($user);

        $payload = [
            'hadith_id' => $hadith->id,
            'note_content' => 'Rahasia Sensitif',
        ];

        $create = $this->postJson('/notes', $payload);
        $create->assertStatus(200)->assertJson(['success' => true]);

        $note = UserNote::where('user_id', $user->id)
            ->where('hadith_id', $hadith->id)
            ->firstOrFail();

        $raw = $note->getRawOriginal('note_content');
        $this->assertNotEquals('Rahasia Sensitif', $raw, 'note_content should be encrypted at rest');
    }

    public function test_note_content_is_decrypted_on_fetch(): void
    {
        $user = $this->createVerifiedUser();
        $hadith = $this->createHadith();

        $this->actingAs($user);

        $this->postJson('/notes', [
            'hadith_id' => $hadith->id,
            'note_content' => 'Rahasia Sensitif',
        ])->assertStatus(200);

        $show = $this->getJson("/notes/{$hadith->id}");
        $show->assertStatus(200)
            ->assertJson([
                'hadith_id' => $hadith->id,
                'note_content' => 'Rahasia Sensitif',
            ]);
    }
}