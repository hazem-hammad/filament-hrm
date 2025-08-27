<?php

use App\Enum\StatusEnum;
use App\Models\Article;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    // Create test articles with unique identifiers to avoid conflicts
    $uniqueId = uniqid();
    $this->activeArticle = Article::factory()->create([
        'title' => "Test Active Article {$uniqueId}",
        'content' => "Test Active content {$uniqueId}",
        'status' => StatusEnum::ACTIVE,
        'date' => '2024-01-01',
        'sort_order' => 1,
    ]);

    $this->inactiveArticle = Article::factory()->create([
        'title' => "Test Inactive Article {$uniqueId}",
        'content' => "Test Inactive content {$uniqueId}",
        'status' => StatusEnum::INACTIVE,
        'date' => '2024-01-02',
        'sort_order' => 2,
    ]);

    $this->secondActiveArticle = Article::factory()->create([
        'title' => "Test Second Active Article {$uniqueId}",
        'content' => "Test Second active content {$uniqueId}",
        'status' => StatusEnum::ACTIVE,
        'date' => '2024-01-03',
        'sort_order' => 3,
    ]);
});

it('returns only active articles', function () {
    $response = $this->getJson('/api/v1/articles');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'content',
                    'date',
                    'image'
                ]
            ]
        ]);

    $articles = $response->json('data');

    // Should include our test active articles (may include others)
    expect($articles)->not->toBeEmpty();

    $articleIds = collect($articles)->pluck('id')->toArray();
    expect($articleIds)->toContain($this->activeArticle->id);
    expect($articleIds)->toContain($this->secondActiveArticle->id);
    expect($articleIds)->not->toContain($this->inactiveArticle->id);
});

it('returns articles with proper structure', function () {
    $response = $this->getJson('/api/v1/articles');

    $response->assertStatus(200);

    $firstArticle = $response->json('data.0');

    expect($firstArticle)->toHaveKeys([
        'id',
        'title',
        'content',
        'date',
        'image'
    ]);

    // Check title and content are strings
    expect($firstArticle['title'])->toBeString();
    expect($firstArticle['content'])->toBeString();

    // Check date format
    expect($firstArticle['date'])->toMatch('/^\d{4}-\d{2}-\d{2}$/');

    // Image should be null when no media is attached
    expect($firstArticle['image'])->toBeNull();
});

it('returns articles sorted by sort_order ascending by default', function () {
    $response = $this->getJson('/api/v1/articles');

    $response->assertStatus(200);

    $articles = $response->json('data');

    // Should include our test articles
    $articleIds = collect($articles)->pluck('id')->toArray();
    expect($articleIds)->toContain($this->activeArticle->id);
    expect($articleIds)->toContain($this->secondActiveArticle->id);

    // Verify ordering by finding positions of our test articles
    $activePos = array_search($this->activeArticle->id, $articleIds);
    $secondActivePos = array_search($this->secondActiveArticle->id, $articleIds);

    // activeArticle (sort_order=1) should come before secondActiveArticle (sort_order=3)
    expect($activePos)->toBeLessThan($secondActivePos);
});

it('supports search by title', function () {
    $response = $this->getJson('/api/v1/articles?search=Active Article');

    $response->assertStatus(200);

    $articles = $response->json('data');
    expect($articles)->toHaveCount(2); // Both active articles match "Active Article"

    // Test search by Spanish title
    $response = $this->getJson('/api/v1/articles?search=Second Active');
    $articles = $response->json('data');
    expect($articles)->toHaveCount(1);
    expect($articles[0]['id'])->toBe($this->secondActiveArticle->id);
});

it('supports search by content', function () {
    $response = $this->getJson('/api/v1/articles?search=Second active content');

    $response->assertStatus(200);

    $articles = $response->json('data');
    expect($articles)->toHaveCount(1);
    expect($articles[0]['id'])->toBe($this->secondActiveArticle->id);
});

it('supports sorting by date', function () {
    $response = $this->getJson('/api/v1/articles?sort_by=date&sort_order=desc');

    $response->assertStatus(200);

    $articles = $response->json('data');

    // Should include our test articles
    $articleIds = collect($articles)->pluck('id')->toArray();
    expect($articleIds)->toContain($this->activeArticle->id);
    expect($articleIds)->toContain($this->secondActiveArticle->id);

    // Find our test articles and verify their dates
    $activeArticle = collect($articles)->firstWhere('id', $this->activeArticle->id);
    $secondActiveArticle = collect($articles)->firstWhere('id', $this->secondActiveArticle->id);

    expect($activeArticle['date'])->toBe('2024-01-01');
    expect($secondActiveArticle['date'])->toBe('2024-01-03');

    // Verify ordering: secondActiveArticle (2024-01-03) should come before activeArticle (2024-01-01) when sorted by date desc
    $activePos = array_search($this->activeArticle->id, $articleIds);
    $secondActivePos = array_search($this->secondActiveArticle->id, $articleIds);
    expect($secondActivePos)->toBeLessThan($activePos);
});

it('supports sorting by created_at', function () {
    $response = $this->getJson('/api/v1/articles?sort_by=created_at&sort_order=asc');

    $response->assertStatus(200);

    $articles = $response->json('data');

    // Should include our test articles
    $articleIds = collect($articles)->pluck('id')->toArray();
    expect($articleIds)->toContain($this->activeArticle->id);
    expect($articleIds)->toContain($this->secondActiveArticle->id);

    // Verify ordering by creation time - activeArticle was created first, so should come first when sorted ascending
    $activePos = array_search($this->activeArticle->id, $articleIds);
    $secondActivePos = array_search($this->secondActiveArticle->id, $articleIds);
    expect($activePos)->toBeLessThan($secondActivePos);
});

it('validates pagination parameters', function () {
    $response = $this->getJson('/api/v1/articles?page=0');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['page']);

    $response = $this->getJson('/api/v1/articles?limit=0');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['limit']);

    $response = $this->getJson('/api/v1/articles?limit=101');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['limit']);
});

it('validates sort parameters', function () {
    $response = $this->getJson('/api/v1/articles?sort_by=invalid_field');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['sort_by']);

    $response = $this->getJson('/api/v1/articles?sort_order=invalid_order');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['sort_order']);
});

it('handles search with no results', function () {
    $response = $this->getJson('/api/v1/articles?search=NonexistentArticle');

    $response->assertStatus(200);

    $articles = $response->json('data');
    expect($articles)->toHaveCount(0);
});

it('returns proper success message', function () {
    $response = $this->getJson('/api/v1/articles');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Articles retrieved successfully'
        ]);
});

it('returns null image when article has no media', function () {
    $response = $this->getJson('/api/v1/articles');

    $response->assertStatus(200);

    $articles = $response->json('data');
    $articleWithoutImage = collect($articles)->firstWhere('id', $this->activeArticle->id);

    expect($articleWithoutImage['image'])->toBeNull();
});

it('handles empty search gracefully', function () {
    $response = $this->getJson('/api/v1/articles?search=');

    $response->assertStatus(200);

    // Empty search should return our active articles (may include others)
    $articles = $response->json('data');
    expect($articles)->not->toBeEmpty();

    $articleIds = collect($articles)->pluck('id')->toArray();
    expect($articleIds)->toContain($this->activeArticle->id);
    expect($articleIds)->toContain($this->secondActiveArticle->id);
});

it('search is case sensitive', function () {
    // Test exact case match
    $response = $this->getJson('/api/v1/articles?search=Active Article');
    $response->assertStatus(200);
    $articles = $response->json('data');
    expect($articles)->toHaveCount(2);

    // Test different case - should return no results
    $response = $this->getJson('/api/v1/articles?search=ACTIVE ARTICLE');
    $response->assertStatus(200);
    $articles = $response->json('data');
    expect($articles)->toHaveCount(0);
});

it('respects pagination parameters', function () {
    // Create more articles to test pagination
    Article::factory()->count(8)->create(['status' => StatusEnum::ACTIVE]);

    $response = $this->getJson('/api/v1/articles?limit=5');
    $response->assertStatus(200);

    $articles = $response->json('data');
    expect($articles)->toHaveCount(5); // Should limit to 5 results
});

it('handles server errors gracefully', function () {
    // Mock the repository to throw an exception instead of the final service
    $this->mock(\App\Repositories\Interfaces\ArticleRepositoryInterface::class)
        ->shouldReceive('list')
        ->andThrow(new \Exception('Database connection failed'));

    $response = $this->getJson('/api/v1/articles');

    $response->assertStatus(500)
        ->assertJson([
            'message' => 'Something went wrong'
        ]);
});
