<?php

use App\Models\Job;
use Illuminate\Http\UploadedFile;

test('job application rate limiting blocks excessive requests', function () {
    $jobData = [
        'first_name' => 'John',
        'last_name' => 'Doe', 
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'years_of_experience' => 5,
        'resume' => UploadedFile::fake()->create('resume.pdf', 1000, 'application/pdf'),
        '_token' => csrf_token(),
    ];

    // Make multiple requests to trigger rate limiting
    for ($i = 0; $i < 4; $i++) {
        $response = $this->post('/careers/test-job/apply', $jobData);
        
        if ($i < 3) {
            // First 3 should succeed or fail due to validation
            expect($response->getStatusCode())->not->toBe(429);
        }
    }

    // 4th request should be rate limited
    $response = $this->post('/careers/test-job/apply', $jobData);
    expect($response->getStatusCode())->toBe(429);
});

test('job application blocks xss attempts', function () {
    $xssPayload = '<script>alert("XSS")</script>';
    
    $jobData = [
        'first_name' => $xssPayload,
        'last_name' => 'Doe',
        'email' => 'john@example.com', 
        'phone' => '+1234567890',
        'years_of_experience' => 5,
        'resume' => UploadedFile::fake()->create('resume.pdf', 1000, 'application/pdf'),
        '_token' => csrf_token(),
    ];

    $response = $this->post('/careers/test-job/apply', $jobData);
    
    // Should get validation error for suspicious content
    $response->assertStatus(422);
    $response->assertJsonValidationErrors('first_name');
});

test('job application blocks sql injection attempts', function () {
    $sqlInjection = "'; DROP TABLE users; --";
    
    $jobData = [
        'first_name' => 'John',
        'last_name' => $sqlInjection,
        'email' => 'john@example.com',
        'phone' => '+1234567890', 
        'years_of_experience' => 5,
        'resume' => UploadedFile::fake()->create('resume.pdf', 1000, 'application/pdf'),
        '_token' => csrf_token(),
    ];

    $response = $this->post('/careers/test-job/apply', $jobData);
    
    // Should get validation error for suspicious content
    $response->assertStatus(422);
    $response->assertJsonValidationErrors('last_name');
});

test('job application validates safe urls', function () {
    $dangerousUrl = 'javascript:alert("XSS")';
    
    $jobData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'years_of_experience' => 5,
        'portfolio_url' => $dangerousUrl,
        'resume' => UploadedFile::fake()->create('resume.pdf', 1000, 'application/pdf'),
        '_token' => csrf_token(),
    ];

    $response = $this->post('/careers/test-job/apply', $jobData);
    
    // Should get validation error for unsafe URL
    $response->assertStatus(422);
    $response->assertJsonValidationErrors('portfolio_url');
});

test('api security headers are added', function () {
    $response = $this->getJson('/api/v1/articles');
    
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('X-XSS-Protection', '1; mode=block');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

test('api rejects requests with suspicious content', function () {
    $response = $this->postJson('/api/v1/contact-us', [
        'name' => '<script>alert("XSS")</script>',
        'email' => 'test@example.com',
        'message' => 'Test message'
    ]);

    expect($response->getStatusCode())->toBe(400);
    $response->assertJson([
        'success' => false,
        'message' => 'Security violation detected.',
        'error_code' => 'SECURITY_VIOLATION'
    ]);
});

test('csrf protection blocks requests without token', function () {
    $jobData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'years_of_experience' => 5,
        'resume' => UploadedFile::fake()->create('resume.pdf', 1000, 'application/pdf'),
        // No CSRF token
    ];

    $response = $this->post('/careers/test-job/apply', $jobData);
    
    // Should be blocked by CSRF protection
    expect($response->getStatusCode())->toBe(419);
});
