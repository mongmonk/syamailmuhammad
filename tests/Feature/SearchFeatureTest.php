<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\Hadith;
use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function search_form_page_renders()
    {
        $response = $this->get('/search/form');

        $response->assertStatus(200);
        $response->assertSee('Pencarian Hadits');
        $response->assertSee('Pencarian Populer');
    }

    /** @test */
    public function advanced_search_returns_results_with_filters()
    {
        $chapterA = Chapter::factory()->create(['chapter_number' => 1, 'title' => 'Iman']);
        $chapterB = Chapter::factory()->create(['chapter_number' => 2, 'title' => 'Shalat']);

        $h1 = Hadith::factory()->create([
            'chapter_id' => $chapterA->id,
            'arabic_text' => 'هذا حديث عن الإيمان',
            'translation' => 'Hadits tentang iman',
            'interpretation' => 'Penjelasan iman',
            'narration_source' => 'Bukhari',
            'hadith_number' => 1,
        ]);

        $h2 = Hadith::factory()->create([
            'chapter_id' => $chapterB->id,
            'arabic_text' => 'هذا حديث عن الصلاة',
            'translation' => 'Hadits tentang shalat',
            'interpretation' => 'Penjelasan shalat',
            'narration_source' => 'Muslim',
            'hadith_number' => 2,
        ]);

        // Query cocok untuk "iman" dan filter bab A
        $response = $this->get('/search/advanced?query=iman&chapter_id=' . $chapterA->id . '&limit=10');

        $response->assertStatus(200);
        $response->assertSee('Hasil Pencarian');
        // Harus memuat hadits di bab A
        $response->assertSee((string) $h1->hadith_number);
        // Tidak memuat hadits di bab B (periksa tautan ke detail hadits tidak ada)
        $response->assertDontSee(url('/hadiths/' . $h2->id));
    }

    /** @test */
    public function popular_queries_display_on_form_page()
    {
        $user = User::factory()->create();

        // Buat data popular query secara agregasi
        SearchHistory::create([
            'user_id' => $user->id,
            'query' => 'iman',
            'results_count' => 1,
        ]);
        SearchHistory::create([
            'user_id' => $user->id,
            'query' => 'iman',
            'results_count' => 2,
        ]);
        SearchHistory::create([
            'user_id' => $user->id,
            'query' => 'shalat',
            'results_count' => 3,
        ]);

        $this->actingAs($user);

        $response = $this->get('/search/form');

        $response->assertStatus(200);
        // Minimal nampak item populer (teks query)
        $response->assertSee('iman');
        $response->assertSee('shalat');
    }

    /** @test */
    public function json_search_with_filters_preserves_structure_and_cached_flag()
    {
        $chapter = Chapter::factory()->create(['chapter_number' => 3, 'title' => 'Sedekah']);

        $h1 = Hadith::factory()->create([
            'chapter_id' => $chapter->id,
            'arabic_text' => 'هذا حديث عن الصدقة',
            'translation' => 'Hadits tentang sedekah utama',
            'interpretation' => 'Penjelasan sedekah dalam',
            'narration_source' => 'Tirmidzi',
            'hadith_number' => 5,
        ]);

        // Query akan cocok pada "sedekah" dan filter bab
        $response = $this->getJson('/search?query=sedekah&chapter_id=' . $chapter->id . '&limit=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'query',
                'results',
                'count',
                'cached',
            ]);

        $data = $response->json();
        $this->assertEquals('sedekah', $data['query']);
        $this->assertTrue($data['cached']); // harus tetap true untuk kompatibilitas test lama
        $this->assertGreaterThanOrEqual(1, $data['count']);
        $this->assertEquals($h1->id, $data['results'][0]['id']);
    }

    /** @test */
    public function authenticated_user_history_is_listed_on_form()
    {
        $user = User::factory()->create();

        // Buat riwayat untuk user
        SearchHistory::create([
            'user_id' => $user->id,
            'query' => 'zakat',
            'results_count' => 2,
        ]);

        $this->actingAs($user);
        $response = $this->get('/search/form');

        $response->assertStatus(200);
        $response->assertSee('Riwayat Pencarian Anda');
        $response->assertSee('zakat');
    }
}