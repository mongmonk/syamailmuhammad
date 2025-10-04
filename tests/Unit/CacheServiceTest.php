<?php

namespace Tests\Unit;

use App\Services\CacheService;
use App\Models\Chapter;
use App\Models\Hadith;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->cacheService = new CacheService();
        
        // Clear cache before each test
        Cache::flush();
    }

    /** @test */
    public function it_can_cache_and_retrieve_chapters()
    {
        // Create test chapters
        $chapters = Chapter::factory()->count(3)->create();
        
        // First call should cache the data
        $result1 = $this->cacheService->getChapters();
        $this->assertEquals(3, $result1->count());
        
        // Second call should retrieve from cache
        $result2 = $this->cacheService->getChapters();
        $this->assertEquals(3, $result2->count());
        
        // Verify both results are identical
        $this->assertEquals($result1->pluck('id'), $result2->pluck('id'));
    }

    /** @test */
    public function it_can_cache_and_retrieve_chapter_with_hadiths()
    {
        // Create test chapter with hadiths
        $chapter = Chapter::factory()->create();
        $hadiths = Hadith::factory()->count(5)->create(['chapter_id' => $chapter->id]);
        
        // First call should cache the data
        $result1 = $this->cacheService->getChapterWithHadiths($chapter->id);
        $this->assertNotNull($result1);
        $this->assertEquals($chapter->id, $result1['chapter']->id);
        $this->assertEquals(5, $result1['hadiths']->count());
        
        // Second call should retrieve from cache
        $result2 = $this->cacheService->getChapterWithHadiths($chapter->id);
        $this->assertNotNull($result2);
        $this->assertEquals($chapter->id, $result2['chapter']->id);
        $this->assertEquals(5, $result2['hadiths']->count());
        
        // Verify both results are identical
        $this->assertEquals($result1['chapter']->id, $result2['chapter']->id);
        $this->assertEquals($result1['hadiths']->pluck('id'), $result2['hadiths']->pluck('id'));
    }

    /** @test */
    public function it_returns_null_for_nonexistent_chapter()
    {
        $result = $this->cacheService->getChapterWithHadiths(999);
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_cache_and_retrieve_hadith_with_related_data()
    {
        // Create test hadith with related data
        $hadith = Hadith::factory()->create();
        
        // First call should cache the data
        $result1 = $this->cacheService->getHadithWithRelatedData($hadith->id);
        $this->assertNotNull($result1);
        $this->assertEquals($hadith->id, $result1['hadith']->id);
        
        // Second call should retrieve from cache
        $result2 = $this->cacheService->getHadithWithRelatedData($hadith->id);
        $this->assertNotNull($result2);
        $this->assertEquals($hadith->id, $result2['hadith']->id);
        
        // Verify both results are identical
        $this->assertEquals($result1['hadith']->id, $result2['hadith']->id);
    }

    /** @test */
    public function it_returns_null_for_nonexistent_hadith()
    {
        $result = $this->cacheService->getHadithWithRelatedData(999);
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_cache_and_retrieve_search_results()
    {
        // Create test hadiths
        Hadith::factory()->count(3)->create(['translation' => 'test search term']);
        Hadith::factory()->count(2)->create(['translation' => 'other content']);
        
        $query = 'test search';
        $limit = 10;
        
        // First call should cache the data
        $result1 = $this->cacheService->getSearchResults($query, $limit);
        $this->assertEquals(3, $result1->count());
        
        // Second call should retrieve from cache
        $result2 = $this->cacheService->getSearchResults($query, $limit);
        $this->assertEquals(3, $result2->count());
        
        // Verify both results are identical
        $this->assertEquals($result1->pluck('id'), $result2->pluck('id'));
    }

    /** @test */
    public function it_can_clear_chapters_cache()
    {
        // Create test chapters
        Chapter::factory()->count(3)->create();
        
        // Cache the data
        $this->cacheService->getChapters();
        
        // Verify data is cached
        $this->assertTrue(Cache::has('chapters:all'));
        
        // Clear cache
        $this->cacheService->clearChaptersCache();
        
        // Verify cache is cleared
        $this->assertFalse(Cache::has('chapters:all'));
    }

    /** @test */
    public function it_can_clear_chapter_with_hadiths_cache()
    {
        // Create test chapter with hadiths
        $chapter = Chapter::factory()->create();
        Hadith::factory()->count(3)->create(['chapter_id' => $chapter->id]);
        
        // Cache the data
        $this->cacheService->getChapterWithHadiths($chapter->id);
        
        // Verify data is cached
        $this->assertTrue(Cache::has('chapters:with_hadiths:' . $chapter->id));
        
        // Clear cache
        $this->cacheService->clearChapterWithHadithsCache($chapter->id);
        
        // Verify cache is cleared
        $this->assertFalse(Cache::has('chapters:with_hadiths:' . $chapter->id));
    }

    /** @test */
    public function it_can_clear_hadith_cache()
    {
        // Create test hadith
        $hadith = Hadith::factory()->create();
        
        // Cache the data
        $this->cacheService->getHadithWithRelatedData($hadith->id);
        
        // Verify data is cached
        $this->assertTrue(Cache::has('hadiths:with_related:' . $hadith->id));
        
        // Clear cache
        $this->cacheService->clearHadithCache($hadith->id);
        
        // Verify cache is cleared
        $this->assertFalse(Cache::has('hadiths:with_related:' . $hadith->id));
    }

    /** @test */
    public function it_can_clear_search_cache()
    {
        // Create test hadiths
        Hadith::factory()->count(3)->create(['translation' => 'test search term']);
        
        $query = 'test search';
        
        // Cache the data
        $this->cacheService->getSearchResults($query);
        
        // Verify data is cached
        $this->assertTrue(Cache::has('search:' . md5($query) . ':limit:20'));
        
        // Clear cache
        $this->cacheService->clearSearchCache($query);
        
        // Verify cache is cleared
        $this->assertFalse(Cache::has('search:' . md5($query) . ':limit:20'));
    }
}