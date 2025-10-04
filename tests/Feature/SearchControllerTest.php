<?php

namespace Tests\Feature;

use App\Models\Hadith;
use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->user = User::factory()->create();
        
        // Create test hadiths
        $this->hadith1 = Hadith::factory()->create([
            'translation' => 'This is a test hadith about faith',
            'arabic_text' => 'هذا حديث اختبار عن الإيمان',
        ]);
        
        $this->hadith2 = Hadith::factory()->create([
            'translation' => 'Another hadith about prayer',
            'arabic_text' => 'حديث آخر عن الصلاة',
        ]);
        
        $this->hadith3 = Hadith::factory()->create([
            'translation' => 'A third hadith about charity',
            'arabic_text' => 'حديث ثالث عن الصدقة',
        ]);
    }

    /** @test */
    public function it_can_search_hadiths_by_translation()
    {
        $response = $this->getJson('/search?query=test hadith');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'query',
                     'results',
                     'count',
                     'cached'
                 ])
                 ->assertJson([
                     'query' => 'test hadith',
                     'count' => 1,
                     'cached' => true
                 ]);
        
        // Verify the correct hadith is returned
        $results = $response->json('results');
        $this->assertEquals($this->hadith1->id, $results[0]['id']);
    }

    /** @test */
    public function it_can_search_hadiths_by_arabic_text()
    {
        $response = $this->getJson('/search?query=الإيمان');
        
        $response->assertStatus(200)
                 ->assertJson([
                     'query' => 'الإيمان',
                     'count' => 1,
                     'cached' => true
                 ]);
        
        // Verify the correct hadith is returned
        $results = $response->json('results');
        $this->assertEquals($this->hadith1->id, $results[0]['id']);
    }

    /** @test */
    public function it_returns_multiple_results_for_matching_terms()
    {
        $response = $this->getJson('/search?query=hadith');
        
        $response->assertStatus(200)
                 ->assertJson([
                     'query' => 'hadith',
                     'count' => 3,
                     'cached' => true
                 ]);
    }

    /** @test */
    public function it_respects_limit_parameter()
    {
        $response = $this->getJson('/search?query=hadith&limit=2');
        
        $response->assertStatus(200)
                 ->assertJson([
                     'query' => 'hadith',
                     'count' => 2,
                     'cached' => true
                 ]);
    }

    /** @test */
    public function it_requires_query_parameter()
    {
        $response = $this->getJson('/search');
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['query']);
    }

    /** @test */
    public function it_validates_query_length()
    {
        // Test minimum length
        $response = $this->getJson('/search?query=a');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['query']);
        
        // Test maximum length
        $response = $this->getJson('/search?query=' . str_repeat('a', 101));
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['query']);
    }

    /** @test */
    public function it_validates_limit_parameter()
    {
        $response = $this->getJson('/search?query=test&limit=0');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['limit']);
        
        $response = $this->getJson('/search?query=test&limit=51');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['limit']);
    }

    /** @test */
    public function it_saves_search_history_for_authenticated_user()
    {
        $this->actingAs($this->user);
        
        $response = $this->getJson('/search?query=test hadith');
        $response->assertStatus(200);
        
        // Verify search history is saved
        $this->assertDatabaseHas('search_history', [
            'user_id' => $this->user->id,
            'query' => 'test hadith',
            'results_count' => 1,
        ]);
    }

    /** @test */
    public function it_does_not_save_search_history_for_guest()
    {
        $response = $this->getJson('/search?query=test hadith');
        $response->assertStatus(200);
        
        // Verify no search history is saved
        $this->assertDatabaseCount('search_history', 0);
    }

    /** @test */
    public function authenticated_user_can_get_search_history()
    {
        $this->actingAs($this->user);
        
        // Create search history
        SearchHistory::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        
        $response = $this->getJson('/search/history');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'history',
                     'count'
                 ])
                 ->assertJson([
                     'count' => 3
                 ]);
    }

    /** @test */
    public function guest_cannot_get_search_history()
    {
        $response = $this->getJson('/search/history');
        
        $response->assertStatus(401)
                 ->assertJson(['error' => 'Unauthenticated']);
    }

    /** @test */
    public function authenticated_user_can_clear_search_history()
    {
        $this->actingAs($this->user);
        
        // Create search history
        SearchHistory::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        
        $response = $this->deleteJson('/search/history');
        
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Search history cleared successfully']);
        
        // Verify search history is deleted
        $this->assertDatabaseCount('search_history', 0);
    }

    /** @test */
    public function guest_cannot_clear_search_history()
    {
        $response = $this->deleteJson('/search/history');
        
        $response->assertStatus(401);
    }

    /** @test */
    public function it_respects_limit_parameter_for_search_history()
    {
        $this->actingAs($this->user);
        
        // Create search history
        SearchHistory::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);
        
        $response = $this->getJson('/search/history?limit=3');
        
        $response->assertStatus(200)
                 ->assertJson([
                     'count' => 3
                 ]);
    }

    /** @test */
    public function it_validates_limit_parameter_for_search_history()
    {
        $this->actingAs($this->user);
        
        $response = $this->getJson('/search/history?limit=0');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['limit']);
        
        $response = $this->getJson('/search/history?limit=21');
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['limit']);
    }
}