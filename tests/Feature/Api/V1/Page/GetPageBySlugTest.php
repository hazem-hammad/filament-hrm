<?php

use App\Enum\StatusEnum;
use App\Models\Page;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $uniqueId = uniqid();

    $this->activePage = Page::factory()->create([
        'title' => [
            'en' => "Privacy Policy {$uniqueId}",
            'es' => "Política de Privacidad {$uniqueId}",
            'ar' => "سياسة الخصوصية {$uniqueId}",
        ],
        'body' => [
            'en' => "This is the privacy policy content {$uniqueId}",
            'es' => "Este es el contenido de la política de privacidad {$uniqueId}",
            'ar' => "هذا هو محتوى سياسة الخصوصية {$uniqueId}",
        ],
        'slug' => "privacy-policy-{$uniqueId}",
        'status' => StatusEnum::ACTIVE,
    ]);

    $this->inactivePage = Page::factory()->create([
        'title' => [
            'en' => "Terms of Service {$uniqueId}",
            'es' => "Términos de Servicio {$uniqueId}",
            'ar' => "شروط الخدمة {$uniqueId}",
        ],
        'body' => [
            'en' => "This is the terms of service content {$uniqueId}",
            'es' => "Este es el contenido de términos de servicio {$uniqueId}",
            'ar' => "هذا هو محتوى شروط الخدمة {$uniqueId}",
        ],
        'slug' => "terms-of-service-{$uniqueId}",
        'status' => StatusEnum::INACTIVE,
    ]);
});

it('returns active page by slug successfully', function () {
    $response = $this->getJson("/api/v1/pages/{$this->activePage->slug}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'title',
                'body',
                'slug',
            ]
        ])
        ->assertJson([
            'data' => [
                'id' => $this->activePage->id,
                'slug' => $this->activePage->slug,
            ]
        ]);
});

it('returns 422 validation error for non-existent page slug', function () {
    $nonExistentSlug = 'non-existent-page-slug-' . uniqid();

    $response = $this->getJson("/api/v1/pages/{$nonExistentSlug}");

    $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'slug'
            ]
        ]);
});

it('returns 422 validation error for inactive page slug', function () {
    $response = $this->getJson("/api/v1/pages/{$this->inactivePage->slug}");

    $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'slug'
            ]
        ]);
});

it('returns page with correct translation structure', function () {
    $response = $this->getJson("/api/v1/pages/{$this->activePage->slug}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'body',
                'slug'
            ]
        ]);

    $data = $response->json('data');

    // Since PageResource returns current locale values, title and body should be strings
    expect($data['title'])->toBeString();
    expect($data['body'])->toBeString();
});

it('returns 500 error when service throws unexpected exception', function () {
    // Mock the service to throw an unexpected exception
    $this->mock(\App\Services\User\Common\InformationService::class, function ($mock) {
        $mock->shouldReceive('getPageBySlug')
            ->andThrow(new \Exception('Unexpected error'));
    });

    $response = $this->getJson("/api/v1/pages/{$this->activePage->slug}");

    $response->assertStatus(500)
        ->assertJsonStructure([
            'message'
        ]);
});

it('returns correct data structure with all expected fields', function () {
    $response = $this->getJson("/api/v1/pages/{$this->activePage->slug}");

    $response->assertStatus(200);

    $data = $response->json('data');

    expect($data)->toHaveKeys(['id', 'title', 'body', 'slug']);
    expect($data['id'])->toBe($this->activePage->id);
    expect($data['slug'])->toBe($this->activePage->slug);
    expect($data['title'])->toBeString();
    expect($data['body'])->toBeString();
});

it('validates slug parameter is required in route', function () {
    $response = $this->getJson('/api/v1/pages/');

    // This should return 404 since the route requires slug parameter
    $response->assertStatus(404);
});
