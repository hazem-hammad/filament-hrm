<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Laravel\Telescope\Telescope;

class CustomLogger
{
    private $context = [];

    private $tags = [];

    private $user = null;

    public function __construct()
    {
        $this->setDefaultContext();
        $this->addDefaultTags();
    }

    /**
     * Set default context for all logs
     */
    private function setDefaultContext()
    {
        $this->context = [
            'environment' => app()->environment(),
            'timestamp' => now()->toIso8601String(),
        ];

        if (auth()->check()) {
            $this->user = auth()->user();
            $this->context['user_id'] = $this->user->id;
            $this->context['user_email'] = $this->user->email ?? null;
            $this->context['user_phone'] = $this->user->phone ?? null;
        }
    }

    /**
     * Add time-based tags automatically
     */
    private function addDefaultTags()
    {
        $now = now();

        // Add hour tag (format: hour_YYYY_MM_DD_HH)
        $hourTag = 'time:hour_'.$now->format('Y_m_d_H');

        // Add day tag (format: day_YYYY_MM_DD)
        $dayTag = 'time:day_'.$now->format('Y_m_d');

        $requestIrl = 'url:'.request()->path();

        $this->tags = array_merge($this->tags, [$hourTag, $dayTag, $requestIrl]);
    }

    /**
     * Add custom tags for telescope
     */
    public function tag(...$tags)
    {
        $this->tags = array_merge($this->tags, $tags);

        return $this;
    }

    /**
     * Add custom context data
     */
    public function withContext(array $context)
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    /**
     * Log an error with stack trace
     */
    public function error($message, ?\Throwable $exception = null)
    {
        $context = $this->context;

        if ($exception) {
            $context['exception'] = [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        $this->log('error', $message, $context);
    }

    /**
     * Log a warning
     */
    public function warning($message, array $additionalContext = [])
    {
        $this->log('warning', $message, $additionalContext);
    }

    /**
     * Log info
     */
    public function info($message, array $additionalContext = [])
    {
        $this->log('info', $message, $additionalContext);
    }

    /**
     * Log debug information
     */
    public function debug($message, array $additionalContext = [])
    {
        $this->log('debug', $message, $additionalContext);
    }

    /**
     * Main logging method
     */
    private function log($level, $message, array $additionalContext = [])
    {
        $this->tag($message);

        $finalContext = array_merge($this->context, $additionalContext);

        // Set Telescope tags
        if (! empty($this->tags)) {
            Telescope::tag(function () {
                return array_unique($this->tags);
            });
        }

        Log::$level($message, $finalContext);
    }
}
