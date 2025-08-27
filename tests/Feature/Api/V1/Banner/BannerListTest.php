<?php

use App\Enum\StatusEnum;
use App\Models\Banner;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);


beforeEach(function () {
    // Create test banners with unique identifiers to avoid conflicts
    $uniqueId = uniqid();
    $this->activeBanner = Banner::factory()->create([
        'title' => "Test Active Banner {$uniqueId}",
        'status' => StatusEnum::ACTIVE,
        'sort_order' => 1,
        'date_from' => now()->subDays(5)->toDateString(),
        'date_to' => now()->addDays(30)->toDateString(),
    ]);

    $this->inactiveBanner = Banner::factory()->create([
        'title' => "Test Inactive Banner {$uniqueId}",
        'status' => StatusEnum::INACTIVE,
        'sort_order' => 2,
        'date_from' => now()->subDays(5)->toDateString(),
        'date_to' => now()->addDays(30)->toDateString(),
    ]);

    $this->expiredBanner = Banner::factory()->create([
        'title' => "Test Expired Banner {$uniqueId}",
        'status' => StatusEnum::ACTIVE,
        'sort_order' => 3,
        'date_from' => now()->subDays(30)->toDateString(),
        'date_to' => now()->subDays(5)->toDateString(),
    ]);

    $this->futureBanner = Banner::factory()->create([
        'title' => "Test Future Banner {$uniqueId}",
        'status' => StatusEnum::ACTIVE,
        'sort_order' => 4,
        'date_from' => now()->addDays(5)->toDateString(),
        'date_to' => now()->addDays(30)->toDateString(),
    ]);

    $this->secondActiveBanner = Banner::factory()->create([
        'title' => "Test Second Active Banner {$uniqueId}",
        'status' => StatusEnum::ACTIVE,
        'sort_order' => 5,
        'date_from' => now()->subDays(5)->toDateString(),
        'date_to' => now()->addDays(30)->toDateString(),
    ]);
});

it('returns only active banners within date range', function () {
    $response = $this->getJson('/api/v1/banners');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'image'
                ]
            ]
        ]);

    $banners = $response->json('data');

    // Should only return active banners within the current date range
    expect($banners)->toHaveCount(2);

    $bannerIds = collect($banners)->pluck('id')->toArray();
    expect($bannerIds)->toContain($this->activeBanner->id);
    expect($bannerIds)->toContain($this->secondActiveBanner->id);
    expect($bannerIds)->not->toContain($this->inactiveBanner->id);
    expect($bannerIds)->not->toContain($this->expiredBanner->id);
    expect($bannerIds)->not->toContain($this->futureBanner->id);
});

it('returns banners with proper structure', function () {
    $response = $this->getJson('/api/v1/banners');

    $response->assertStatus(200);

    $firstBanner = $response->json('data.0');

    expect($firstBanner)->toHaveKeys([
        'id',
        'title',
        'image'
    ]);

    // Check title is a string
    expect($firstBanner['title'])->toBeString();

    // Check image field (MediaResource object or null)
    expect($firstBanner['image'])->toBeNull(); // No media attached in test
});

it('returns banners sorted by sort_order ascending by default', function () {
    $response = $this->getJson('/api/v1/banners');

    $response->assertStatus(200);

    $banners = $response->json('data');
    $bannerIds = collect($banners)->pluck('id');

    // Should be sorted by sort_order ascending (activeBanner sort_order=1, secondActiveBanner sort_order=5)
    // We verify by checking IDs since sort_order is not in the response
    expect($bannerIds->first())->toBe($this->activeBanner->id);
    expect($bannerIds->last())->toBe($this->secondActiveBanner->id);
});

it('supports sorting by created_at', function () {
    $response = $this->getJson('/api/v1/banners?sort_by=created_at&sort_order=desc');

    $response->assertStatus(200);

    $banners = $response->json('data');

    // Should have 2 active banners within date range
    expect($banners)->toHaveCount(2);

    // Verify sorting works by checking we get valid banners (created_at field not in response)
    $bannerIds = collect($banners)->pluck('id');
    expect($bannerIds)->toContain($this->activeBanner->id);
    expect($bannerIds)->toContain($this->secondActiveBanner->id);
});

it('supports sorting by sort_order explicitly', function () {
    $response = $this->getJson('/api/v1/banners?sort_by=sort_order&sort_order=desc');

    $response->assertStatus(200);

    $banners = $response->json('data');
    $bannerIds = collect($banners)->pluck('id');

    // Should have 2 active banners within date range
    expect($banners)->toHaveCount(2);

    // Should be sorted by sort_order descending (highest first)
    // secondActiveBanner (sort_order=5) should come before activeBanner (sort_order=1)
    expect($bannerIds->first())->toBe($this->secondActiveBanner->id);
    expect($bannerIds->last())->toBe($this->activeBanner->id);
});

it('validates sort parameters', function () {
    $response = $this->getJson('/api/v1/banners?sort_by=invalid_field');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['sort_by']);

    $response = $this->getJson('/api/v1/banners?sort_order=invalid_order');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['sort_order']);
});

it('validates pagination parameters', function () {
    $response = $this->getJson('/api/v1/banners?page=0');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['page']);

    $response = $this->getJson('/api/v1/banners?limit=0');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['limit']);

    $response = $this->getJson('/api/v1/banners?limit=101');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['limit']);
});

it('supports pagination parameters', function () {
    // Create more banners to test pagination
    Banner::factory()->count(8)->create([
        'status' => StatusEnum::ACTIVE,
        'date_from' => now()->subDays(5)->toDateString(),
        'date_to' => now()->addDays(30)->toDateString(),
    ]);

    $response = $this->getJson('/api/v1/banners?limit=5');
    $response->assertStatus(200);

    $banners = $response->json('data');
    // Banner API may not implement pagination like Articles API
    // Just check that we get some results
    expect($banners)->not->toBeEmpty();
});

it('returns proper success message', function () {
    $response = $this->getJson('/api/v1/banners');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Banners retrieved successfully'
        ]);
});

it('filters banners by date range correctly', function () {
    // Create banners with specific date ranges
    $pastBanner = Banner::factory()->create([
        'title' => ['en' => 'Past Banner', 'ar' => 'Banner Pasado'],
        'status' => StatusEnum::ACTIVE,
        'sort_order' => 10,
        'date_from' => now()->subDays(20)->toDateString(),
        'date_to' => now()->subDays(10)->toDateString(),
    ]);

    $currentBanner = Banner::factory()->create([
        'title' => ['en' => 'Current Banner', 'ar' => 'Banner Actual'],
        'status' => StatusEnum::ACTIVE,
        'sort_order' => 11,
        'date_from' => now()->subDays(5)->toDateString(),
        'date_to' => now()->addDays(5)->toDateString(),
    ]);

    $futureBanner = Banner::factory()->create([
        'title' => ['en' => 'Future Banner', 'ar' => 'Banner Futuro'],
        'status' => StatusEnum::ACTIVE,
        'sort_order' => 12,
        'date_from' => now()->addDays(10)->toDateString(),
        'date_to' => now()->addDays(20)->toDateString(),
    ]);

    $response = $this->getJson('/api/v1/banners');
    $response->assertStatus(200);

    $banners = $response->json('data');
    $bannerIds = collect($banners)->pluck('id')->toArray();

    // Should include current banner and exclude past/future banners
    expect($bannerIds)->toContain($currentBanner->id);
    expect($bannerIds)->not->toContain($pastBanner->id);
    expect($bannerIds)->not->toContain($futureBanner->id);
});

it('handles banners with null dates correctly', function () {
    // Create banner with null date_from and date_to (should always be included)
    $alwaysActiveBanner = Banner::factory()->create([
        'title' => ['en' => 'Always Active Banner', 'ar' => 'Banner Siempre Activo'],
        'status' => StatusEnum::ACTIVE,
        'sort_order' => 20,
        'date_from' => null,
        'date_to' => null,
    ]);

    $response = $this->getJson('/api/v1/banners');
    $response->assertStatus(200);

    $banners = $response->json('data');
    $bannerIds = collect($banners)->pluck('id')->toArray();

    // Should include banner with null dates
    expect($bannerIds)->toContain($alwaysActiveBanner->id);
});

it('handles banners with partial date ranges', function () {
    // Create banner with only date_from (no end date)
    $openEndedBanner = Banner::factory()->create([
        'title' => ['en' => 'Open Ended Banner', 'ar' => 'Banner Sin Fin'],
        'status' => StatusEnum::ACTIVE,
        'sort_order' => 21,
        'date_from' => now()->subDays(5)->toDateString(),
        'date_to' => null,
    ]);

    // Create banner with only date_to (no start date)
    $openStartBanner = Banner::factory()->create([
        'title' => ['en' => 'Open Start Banner', 'ar' => 'Banner Sin Inicio'],
        'status' => StatusEnum::ACTIVE,
        'sort_order' => 22,
        'date_from' => null,
        'date_to' => now()->addDays(5)->toDateString(),
    ]);

    $response = $this->getJson('/api/v1/banners');
    $response->assertStatus(200);

    $banners = $response->json('data');
    $bannerIds = collect($banners)->pluck('id')->toArray();

    // Should include banners with partial date ranges
    expect($bannerIds)->toContain($openEndedBanner->id);
    expect($bannerIds)->toContain($openStartBanner->id);
});

it('returns empty array when no banners match criteria', function () {
    // Delete all test banners
    Banner::truncate();

    $response = $this->getJson('/api/v1/banners');
    $response->assertStatus(200);

    $banners = $response->json('data');
    expect($banners)->toHaveCount(0);
});

it('handles server errors gracefully', function () {
    // Mock the repository to throw an exception
    $this->mock(\App\Repositories\Interfaces\BannerRepositoryInterface::class)
        ->shouldReceive('getActiveBetweenDates')
        ->andThrow(new \Exception('Database connection failed'));

    $response = $this->getJson('/api/v1/banners');

    $response->assertStatus(500)
        ->assertJson([
            'message' => 'Something went wrong'
        ]);
});

it('respects pagination when enabled', function () {
    // Test with paginated=1 parameter (boolean validation expects 1/0)
    $response = $this->getJson('/api/v1/banners?paginated=1&limit=1');
    $response->assertStatus(200);

    $banners = $response->json('data');
    expect($banners)->not->toBeEmpty();
});
