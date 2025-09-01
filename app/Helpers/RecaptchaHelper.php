<?php

namespace App\Helpers;

class RecaptchaHelper
{
    /**
     * Generate reCAPTCHA script tag based on version
     */
    public static function getScriptTag(): string
    {
        if (!config('recaptcha.enabled')) {
            return '';
        }

        if (config('recaptcha.version') === 'v2') {
            return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        }

        // v3
        $siteKey = config('recaptcha.site_key');
        return "<script src=\"https://www.google.com/recaptcha/api.js?render={$siteKey}\"></script>";
    }

    /**
     * Generate reCAPTCHA HTML element for forms
     */
    public static function getHtmlElement(): string
    {
        if (!config('recaptcha.enabled')) {
            return '<input type="hidden" name="g-recaptcha-response" value="test-token">';
        }

        if (config('recaptcha.version') === 'v2') {
            $siteKey = config('recaptcha.site_key');
            return "
                <div class=\"g-recaptcha\" 
                     data-sitekey=\"{$siteKey}\"
                     data-theme=\"light\"
                     data-size=\"normal\">
                </div>
            ";
        }

        // v3 - hidden input filled by JavaScript
        return '<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">';
    }

    /**
     * Generate JavaScript for reCAPTCHA v3
     */
    public static function getV3JavaScript(): string
    {
        if (!config('recaptcha.enabled') || config('recaptcha.version') !== 'v3') {
            return '';
        }

        $siteKey = config('recaptcha.site_key');
        $action = config('recaptcha.action');

        return "
            let recaptchaReady = false;
            grecaptcha.ready(function() {
                recaptchaReady = true;
            });

            function executeRecaptcha() {
                return new Promise((resolve, reject) => {
                    if (!recaptchaReady) {
                        reject('reCAPTCHA is still loading. Please try again.');
                        return;
                    }

                    grecaptcha.execute('{$siteKey}', {action: '{$action}'}).then(function(token) {
                        document.getElementById('g-recaptcha-response').value = token;
                        resolve(token);
                    }).catch(reject);
                });
            }
        ";
    }

    /**
     * Get validation JavaScript for forms
     */
    public static function getValidationJavaScript(): string
    {
        if (!config('recaptcha.enabled')) {
            return 'return true;';
        }

        if (config('recaptcha.version') === 'v2') {
            return "
                const recaptchaResponse = grecaptcha.getResponse();
                if (!recaptchaResponse) {
                    showRecaptchaError('Please complete the reCAPTCHA verification.');
                    return false;
                }
                return true;
            ";
        }

        // v3 - handled by executeRecaptcha promise
        return 'return true;';
    }
}