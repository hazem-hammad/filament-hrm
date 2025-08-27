<?php

use App\Models\ContactUs;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

beforeEach(function () {
    // Additional cleanup if needed - but transactions will handle rollback
    // This runs within a transaction that gets rolled back after each test
});

describe('Contact Us API', function () {
    
    it('successfully stores a contact us request with valid data', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'subject' => 'General Inquiry',
            'message' => 'This is a test message for contact us functionality.'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Contact Us request submitted successfully'
            ]);

        // Verify data was stored in database
        $this->assertDatabaseHas('contact_us', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'subject' => 'General Inquiry',
            'message' => 'This is a test message for contact us functionality.',
            'is_read' => false
        ]);
    });

    it('returns validation error when name is missing', function () {
        $data = [
            'email' => 'john.doe@example.com',
            'subject' => 'General Inquiry',
            'message' => 'This is a test message.'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    });

    it('returns validation error when email is missing', function () {
        $data = [
            'name' => 'John Doe',
            'subject' => 'General Inquiry',
            'message' => 'This is a test message.'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('returns validation error when subject is missing', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'message' => 'This is a test message.'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject']);
    });

    it('returns validation error when message is missing', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'subject' => 'General Inquiry'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    });

    it('returns validation error when all fields are missing', function () {
        $response = $this->postJson('/api/v1/contact-us', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);
    });

    it('returns validation error when name exceeds maximum length', function () {
        $data = [
            'name' => str_repeat('a', 256), // Exceeds 255 character limit
            'email' => 'john.doe@example.com',
            'subject' => 'General Inquiry',
            'message' => 'This is a test message.'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    });

    it('returns validation error when email exceeds maximum length', function () {
        $data = [
            'name' => 'John Doe',
            'email' => str_repeat('a', 250) . '@example.com', // Exceeds 255 character limit
            'subject' => 'General Inquiry',
            'message' => 'This is a test message.'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('returns validation error when subject exceeds maximum length', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'subject' => str_repeat('a', 256), // Exceeds 255 character limit
            'message' => 'This is a test message.'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject']);
    });

    it('returns validation error when message exceeds maximum length', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'subject' => 'General Inquiry',
            'message' => str_repeat('a', 1001) // Exceeds 1000 character limit (validation rule)
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    });

    it('handles empty string values properly', function () {
        $data = [
            'name' => '',
            'email' => '',
            'subject' => '',
            'message' => ''
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);
    });

    it('handles whitespace-only values properly', function () {
        $data = [
            'name' => '   ',
            'email' => '   ',
            'subject' => '   ',
            'message' => '   '
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);
    });

    it('successfully stores contact us request with maximum allowed lengths', function () {
        $data = [
            'name' => str_repeat('a', 255), // Maximum allowed length
            'email' => str_repeat('b', 240) . '@example.com', // Maximum allowed length (255 total)
            'subject' => str_repeat('c', 255), // Maximum allowed length
            'message' => str_repeat('d', 255) // Maximum allowed length (database constraint)
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Contact Us request submitted successfully'
            ]);

        $this->assertDatabaseHas('contact_us', [
            'name' => str_repeat('a', 255),
            'email' => str_repeat('b', 240) . '@example.com',
            'subject' => str_repeat('c', 255),
            'message' => str_repeat('d', 255),
            'is_read' => false
        ]);
    });

    it('stores multiple contact us requests independently', function () {
        $data1 = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'First Inquiry',
            'message' => 'First message'
        ];

        $data2 = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'subject' => 'Second Inquiry',
            'message' => 'Second message'
        ];

        $response1 = $this->postJson('/api/v1/contact-us', $data1);
        $response2 = $this->postJson('/api/v1/contact-us', $data2);

        $response1->assertStatus(201);
        $response2->assertStatus(201);

        $this->assertDatabaseHas('contact_us', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $this->assertDatabaseHas('contact_us', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com'
        ]);

        // Verify both records exist
        $this->assertDatabaseCount('contact_us', 2);
    });

    it('handles special characters in input fields', function () {
        $data = [
            'name' => 'José María O\'Connor',
            'email' => 'jose.maria@example.com',
            'subject' => 'Question about "Product" & Service',
            'message' => 'This message contains special characters: @#$%^&*()_+[]{}|;":,.<>?'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('contact_us', [
            'name' => 'José María O\'Connor',
            'email' => 'jose.maria@example.com',
            'subject' => 'Question about "Product" & Service',
            'message' => 'This message contains special characters: @#$%^&*()_+[]{}|;":,.<>?'
        ]);
    });

    it('respects rate limiting for post_data throttle', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        // Make multiple requests quickly to test rate limiting
        // The post_data throttle should limit to 5 requests per minute
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/v1/contact-us', $data);
            
            if ($i < 5) {
                $response->assertStatus(201);
            } else {
                // The 6th request should be rate limited
                $response->assertStatus(429);
            }
        }
    });

    it('has correct response structure on success', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message'
            ])
            ->assertJson([
                'message' => 'Contact Us request submitted successfully'
            ]);
    });

    it('has correct response headers', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);

        $response->assertStatus(201)
            ->assertHeader('Content-Type', 'application/json');
    });

    it('sets is_read to false by default for new contact us entries', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $this->postJson('/api/v1/contact-us', $data);

        $contactUs = ContactUs::where('email', 'john@example.com')->first();
        expect($contactUs->is_read)->toBe(false);
    });

    it('creates database record with timestamps', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $this->postJson('/api/v1/contact-us', $data);

        $contactUs = ContactUs::where('email', 'john@example.com')->first();
        expect($contactUs->created_at)->not->toBeNull();
        expect($contactUs->updated_at)->not->toBeNull();
    });
});