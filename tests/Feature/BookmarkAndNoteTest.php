<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Chapter;
use App\Models\Hadith;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkAndNoteTest extends TestCase
{
    use RefreshDatabase;

    protected function createVerifiedUser(): User
    {
        /** @var User $user */
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

    public function test_user_can_add_and_delete_bookmark(): void
    {
        $user = $this->createVerifiedUser();
        $hadith = $this->createHadith();

        $this->actingAs($user);

        // Tambah bookmark
        $response = $this->postJson('/bookmarks', [
            'hadith_id' => $hadith->id,
            'notes' => 'Catatan bookmark awal',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'hadith_id' => $hadith->id,
            'notes' => 'Catatan bookmark awal',
        ]);

        // Update notes pada bookmark
        $update = $this->putJson("/bookmarks/{$hadith->id}", [
            'notes' => 'Catatan bookmark diubah',
        ]);

        $update->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'hadith_id' => $hadith->id,
            'notes' => 'Catatan bookmark diubah',
        ]);

        // Hapus bookmark
        $delete = $this->deleteJson("/bookmarks/{$hadith->id}");

        $delete->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'hadith_id' => $hadith->id,
        ]);
    }

    public function test_user_can_add_update_fetch_and_delete_note(): void
    {
        $user = $this->createVerifiedUser();
        $hadith = $this->createHadith();

        $this->actingAs($user);

        // Tambah catatan
        $create = $this->postJson('/notes', [
            'hadith_id' => $hadith->id,
            'note_content' => 'Catatan awal',
        ]);

        $create->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $noteDb1 = \App\Models\UserNote::where('user_id', $user->id)
            ->where('hadith_id', $hadith->id)
            ->firstOrFail();
        $this->assertNotEquals('Catatan awal', $noteDb1->getRawOriginal('note_content'));

        // Ambil catatan (JSON)
        $show = $this->getJson("/notes/{$hadith->id}");
        $show->assertStatus(200)
            ->assertJson([
                'hadith_id' => $hadith->id,
                'note_content' => 'Catatan awal',
            ]);

        // Update catatan
        $update = $this->putJson("/notes/{$hadith->id}", [
            'note_content' => 'Catatan diubah',
        ]);

        $update->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $noteDb2 = \App\Models\UserNote::where('user_id', $user->id)
            ->where('hadith_id', $hadith->id)
            ->firstOrFail();
        $this->assertNotEquals('Catatan diubah', $noteDb2->getRawOriginal('note_content'));

        // Hapus catatan
        $delete = $this->deleteJson("/notes/{$hadith->id}");

        $delete->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('user_notes', [
            'user_id' => $user->id,
            'hadith_id' => $hadith->id,
        ]);
    }

    public function test_bookmarks_index_view_renders(): void
    {
        $user = $this->createVerifiedUser();
        $hadith = $this->createHadith();

        $this->actingAs($user);

        // Buat satu bookmark
        $this->postJson('/bookmarks', [
            'hadith_id' => $hadith->id,
            'notes' => 'Catatan bookmark',
        ])->assertStatus(200); // redirect validation HTML, tidak masalah

        $response = $this->get('/bookmarks');
        $response->assertStatus(200);
        $response->assertSee('Bookmark Saya');
        $response->assertSee((string) $hadith->hadith_number);
        $response->assertSee('Catatan Bookmark');
    }

    public function test_notes_index_view_renders(): void
    {
        $user = $this->createVerifiedUser();
        $hadith = $this->createHadith();

        $this->actingAs($user);

        // Buat satu catatan
        $this->postJson('/notes', [
            'hadith_id' => $hadith->id,
            'note_content' => 'Isi Catatan',
        ])->assertStatus(200); // untuk request non-json, Laravel redirect jika validasi sukses

        $response = $this->get('/notes');
        $response->assertStatus(200);
        $response->assertSee('Catatan Saya');
        $response->assertSee('Isi Catatan');
        $response->assertSee((string) $hadith->hadith_number);
    }
}