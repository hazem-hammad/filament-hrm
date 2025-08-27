# Contact Us API Tests

This test suite provides comprehensive coverage for the Contact Us API endpoint (`POST /api/v1/contact-us`).

## Test Coverage

### ✅ Success Scenarios
- Successfully stores valid contact us requests
- Handles maximum field lengths correctly
- Stores multiple requests independently
- Handles special characters in input fields
- Sets correct default values (is_read = false)
- Creates proper timestamps

### ✅ Validation Testing
- **Required Fields**: Tests missing name, email, subject, and message
- **Field Length Limits**: Tests maximum length validation for all fields
- **Empty Values**: Tests empty strings and whitespace-only values
- **Combined Validation**: Tests multiple validation errors simultaneously

### ✅ Rate Limiting
- Tests `post_data` throttle middleware (5 requests per minute limit)
- Verifies proper 429 status code when limit exceeded

### ✅ Response Structure
- Validates correct HTTP status codes (201 for success, 422 for validation errors)
- Checks response JSON structure and content
- Verifies proper Content-Type headers

### ✅ Database Integration
- Confirms data persistence in `contact_us` table
- Validates proper field mapping from request to database
- Checks default values and timestamps

## Field Constraints

| Field | Type | Max Length | Required | Notes |
|-------|------|------------|----------|-------|
| name | string | 255 | ✓ | User's full name |
| email | string | 255 | ✓ | Contact email address |
| subject | string | 255 | ✓ | Message subject |
| message | string | 255* | ✓ | Message content |
| is_read | boolean | - | - | Default: false |

*Note: Validation allows up to 1000 characters, but database constraint is 255 characters*

## Running Tests

```bash
# Run all Contact Us tests
php artisan test tests/Feature/Api/V1/ContactUs/ContactUsTest.php

# Run specific test
php artisan test --filter="successfully stores a contact us request"

# Run with coverage
php artisan test tests/Feature/Api/V1/ContactUs/ --coverage
```

## Test Scenarios Covered

1. **Happy Path**: Valid data submission
2. **Validation Errors**: Missing and invalid fields  
3. **Edge Cases**: Maximum lengths, special characters
4. **Rate Limiting**: Throttle middleware testing
5. **Database**: Data persistence and integrity
6. **Response Format**: JSON structure validation

## API Endpoint Details

- **URL**: `POST /api/v1/contact-us`
- **Middleware**: `throttle:post_data` (5 requests/minute)
- **Authentication**: Not required
- **Response**: JSON with success message
- **Status Codes**: 
  - `201`: Success
  - `422`: Validation Error
  - `429`: Rate Limited
  - `500`: Server Error