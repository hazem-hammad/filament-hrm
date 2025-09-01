# Google reCAPTCHA v3 Setup Guide

This guide explains how to configure Google reCAPTCHA v3 for the HRM application.

## What is reCAPTCHA v3?

reCAPTCHA v3 is Google's invisible CAPTCHA solution that:
- Runs in the background without user interaction
- Provides a score (0.0 to 1.0) indicating the likelihood that the user is human
- Allows for better user experience while maintaining security
- Uses machine learning to detect bot behavior

## Configuration

### 1. Environment Variables

Add these variables to your `.env` file:

```env
# Google reCAPTCHA Configuration
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
RECAPTCHA_ENABLED=true
RECAPTCHA_VERSION=v3
RECAPTCHA_SCORE_THRESHOLD=0.5
RECAPTCHA_ACTION=job_application
RECAPTCHA_SKIP_TESTING=false
```

### 2. Obtain reCAPTCHA Keys

1. Visit [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Create a new site
3. Select **reCAPTCHA v3**
4. Add your domain(s)
5. Copy the Site Key and Secret Key

### 3. Configuration Options

| Variable | Description | Default | Recommended |
|----------|-------------|---------|-------------|
| `RECAPTCHA_SITE_KEY` | Public key for frontend | - | Required |
| `RECAPTCHA_SECRET_KEY` | Private key for backend verification | - | Required |
| `RECAPTCHA_ENABLED` | Enable/disable reCAPTCHA | `true` | `true` |
| `RECAPTCHA_VERSION` | Version (v2 or v3) | `v3` | `v3` |
| `RECAPTCHA_SCORE_THRESHOLD` | Minimum score to pass (0.0-1.0) | `0.5` | `0.5` |
| `RECAPTCHA_ACTION` | Action name for this form | `job_application` | Custom per form |
| `RECAPTCHA_SKIP_TESTING` | Skip validation in tests | `false` | `false` |

## Score Threshold Guidelines

| Score Range | Recommendation | Use Case |
|-------------|----------------|----------|
| 0.9 - 1.0 | Very likely human | Allow without additional verification |
| 0.7 - 0.9 | Likely human | Allow with light monitoring |
| 0.3 - 0.7 | Neutral | **Default threshold** - Request additional verification |
| 0.1 - 0.3 | Likely bot | Block or require additional verification |
| 0.0 - 0.1 | Very likely bot | Block the request |

## Implementation Details

### Frontend Integration

The job application form automatically includes:

1. **Script Loading**: reCAPTCHA v3 script loads automatically
2. **Token Generation**: JavaScript executes reCAPTCHA on form submission
3. **Hidden Field**: Token is placed in `g-recaptcha-response` field

### Backend Validation

Validation occurs in multiple layers:

1. **RecaptchaMiddleware**: Applied to job application routes
2. **SecureJobApplicationRequest**: Form request with built-in validation
3. **Score Verification**: Automatic score threshold checking
4. **Action Verification**: Ensures the action matches expected value

### Security Features

- **IP Address Tracking**: All attempts logged with IP
- **Score Logging**: Failed attempts include score details
- **Action Verification**: Prevents token reuse across different forms
- **Rate Limiting**: Combined with rate limiting middleware
- **Error Logging**: Comprehensive logging for monitoring

## Monitoring and Logging

### Success Logs
```
[INFO] reCAPTCHA validation successful
{
    "ip": "192.168.1.1",
    "score": 0.8,
    "action": "job_application"
}
```

### Failure Logs
```
[WARNING] reCAPTCHA score too low
{
    "ip": "192.168.1.1", 
    "score": 0.2,
    "threshold": 0.5,
    "action": "job_application",
    "url": "/careers/developer/apply"
}
```

## Testing

### Development Testing

Set `RECAPTCHA_SKIP_TESTING=true` in your `.env` file to disable reCAPTCHA during development.

### Automated Testing

The application automatically skips reCAPTCHA validation during automated tests when `APP_ENV=testing`.

### Manual Testing

1. Use reCAPTCHA test keys (always pass):
   - Site Key: `6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI`
   - Secret Key: `6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe`

2. Monitor logs for score values
3. Test with different user agents and behaviors

## Troubleshooting

### Common Issues

1. **"reCAPTCHA verification failed"**
   - Check site key configuration
   - Verify domain is added to reCAPTCHA admin
   - Ensure network connectivity to Google

2. **"Score too low" errors**
   - Lower the threshold temporarily
   - Check for automation/bot-like behavior
   - Review user agent patterns

3. **"Action mismatch" errors**
   - Verify `RECAPTCHA_ACTION` matches frontend
   - Check for token reuse
   - Ensure action is correctly configured

### Debug Mode

Enable detailed logging by setting `LOG_LEVEL=debug` in your `.env` file.

## Security Best Practices

1. **Never expose secret key**: Keep `RECAPTCHA_SECRET_KEY` secure
2. **Use HTTPS**: reCAPTCHA requires secure connections in production
3. **Monitor scores**: Set up alerts for unusual score patterns
4. **Regular updates**: Keep the Google reCAPTCHA library updated
5. **Backup verification**: Combine with other security measures (rate limiting, etc.)

## Migration from v2

If migrating from reCAPTCHA v2:

1. Change `RECAPTCHA_VERSION=v3`
2. Update site configuration in Google admin
3. Set appropriate score threshold
4. Test thoroughly before production deployment

The application supports both versions, so you can test v3 alongside v2 before switching.