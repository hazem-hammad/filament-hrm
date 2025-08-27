# Safe Database Testing Guide

## ⚠️ Important: Database Testing Strategy

**NEVER use `RefreshDatabase` in testing or production environments!** It completely wipes and rebuilds your database, which can:
- Delete all existing data permanently
- Break ongoing processes
- Cause data loss in production

## ✅ Recommended Approaches

### 1. **DatabaseTransactions** (Recommended for most cases)
```php
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);
```

**How it works:**
- Wraps each test in a database transaction
- Automatically rolls back after each test completes
- **Safe**: No permanent changes to database
- **Fast**: No database rebuilding required
- **Isolated**: Each test runs in clean state

**Best for:** API tests, feature tests, integration tests

### 2. **Manual Cleanup** (For specific scenarios)
```php
beforeEach(function () {
    // Setup test data
});

afterEach(function () {
    // Clean up specific test data
    DB::table('contact_us')->where('email', 'like', '%test%')->delete();
});
```

**Best for:** Tests that need specific cleanup logic

### 3. **Test Database** (Separate database)
Configure a separate test database in `phpunit.xml`:
```xml
<env name="DB_DATABASE" value="your_app_test"/>
```

**Best for:** When you need complete isolation

## 🚫 When NOT to Use RefreshDatabase

- ❌ Testing environments with real data
- ❌ Staging servers
- ❌ Production servers (NEVER!)
- ❌ Shared development databases
- ❌ CI/CD pipelines with persistent data

## ✅ When RefreshDatabase is OK

- ✅ Local development with disposable test data
- ✅ Isolated test databases
- ✅ Docker containers that get destroyed
- ✅ In-memory SQLite databases

## Environment-Specific Configuration

### phpunit.xml Configuration
```xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <!-- Only use RefreshDatabase with in-memory DB -->
</php>
```

### Conditional Database Strategy
```php
// Use different strategies based on environment
if (config('database.default') === 'sqlite' && config('database.connections.sqlite.database') === ':memory:') {
    uses(RefreshDatabase::class); // Safe with in-memory DB
} else {
    uses(DatabaseTransactions::class); // Safe with real databases
}
```

## Test Data Management

### Factory Usage with Transactions
```php
beforeEach(function () {
    // Create test data - will be rolled back automatically
    $this->user = User::factory()->create();
    $this->article = Article::factory()->create(['user_id' => $this->user->id]);
});
```

### Seeded Data Testing
```php
// Test against existing seeded data
it('can retrieve articles', function () {
    $response = $this->getJson('/api/v1/articles');
    
    $response->assertStatus(200);
    // Assert structure without assuming specific data
    $response->assertJsonStructure(['data', 'meta', 'links']);
});
```

## Migration to Safe Testing

### Step 1: Update Test Files
Replace all instances:
```php
// BEFORE (Dangerous)
uses(RefreshDatabase::class);

// AFTER (Safe)
uses(DatabaseTransactions::class);
```

### Step 2: Update Test Logic
```php
// Remove manual truncation/deletion
beforeEach(function () {
    // DB::table('table')->truncate(); // ❌ Remove this
    
    // Just create test data - transactions handle cleanup
    $this->testData = Model::factory()->create();
});
```

### Step 3: Verify Tests Still Pass
```bash
# Run tests to ensure they still work
php artisan test

# Run specific test suite
php artisan test tests/Feature/Api/V1/ContactUs/
```

## Performance Considerations

### DatabaseTransactions Performance
- **Fast**: No schema rebuilding
- **Efficient**: Only rollback changes
- **Scalable**: Works with large databases

### RefreshDatabase Performance  
- **Slow**: Rebuilds entire schema
- **Resource intensive**: Drops/creates tables
- **Not scalable**: Gets slower with more migrations

## Best Practices Summary

1. ✅ **Use `DatabaseTransactions`** for most tests
2. ✅ **Use separate test database** when possible
3. ✅ **Test data isolation** with factories
4. ✅ **Environment-aware** test strategies
5. ❌ **Never use `RefreshDatabase`** on shared/production DBs
6. ✅ **Document your testing approach** for team consistency

## Updated Test Structure Example

```php
<?php

use App\Models\ContactUs;
use Illuminate\Foundation\Testing\DatabaseTransactions;

// Safe for all environments
uses(DatabaseTransactions::class);

describe('Contact Us API', function () {
    it('stores contact request successfully', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
        ];

        $response = $this->postJson('/api/v1/contact-us', $data);
        
        $response->assertStatus(201);
        
        // Data will be automatically rolled back after test
        $this->assertDatabaseHas('contact_us', [
            'email' => 'john@test.com'
        ]);
    });
});
```

This approach ensures your tests are **safe**, **fast**, and **reliable** across all environments! 🚀