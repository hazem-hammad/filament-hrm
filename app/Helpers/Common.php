<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

function getLocalizedMedia($model, $collection, $locale = null)
{
    $locale = $locale ?? app()->getLocale();

    return $collection . '_' . strtolower($locale);
}


function generateOtp()
{
    $otp = mt_rand(1000, 9999);
    if (config('core.otp.fixed_mode')) {
        $otp = 1234;
    }

    return $otp;
}

function getIfSet($data, $key, $default = null)
{
    return $data[$key] ?? $default;
}

function getLocalizedKey($model, string $column, $locale = null)
{
    if (! $model) {
        return '';
    }

    $locale = $locale ?? app()->getLocale();

    return $model[$column][$locale] ?? null;
}

function attachMedia($model, string|array|null $paths, string $collection): void
{
    if (empty($paths)) {
        return;
    }
    foreach ((array) $paths as $path) {
        $model->addMediaFromDisk($path)
            ->toMediaCollection($collection);
    }
}

function booleanValue($input)
{
    if (is_bool($input)) {
        return $input;
    }

    if ($input === 0) {
        return false;
    }

    if ($input === 1) {
        return true;
    }

    if (is_string($input)) {
        switch (strtolower($input)) {
            case 'true':
            case 'on':
            case '1':
                return true;
                break;

            case 'false':
            case 'off':
            case '0':
                return false;
                break;
        }
    }

    return null;
}

function formatDate(string $date, string $fromFormat = 'd-m-Y', string $toFormat = 'Y-m-d'): ?string
{
    try {
        return Carbon::createFromFormat($fromFormat, $date)->format($toFormat);
    } catch (\Exception $e) {
        return null;
    }
}

function applyLocalizedSearch(Builder $query, array $locales, string $searchTerm, array $fields): void
{
    foreach ($locales as $locale) {
        foreach ($fields as $field) {
            $jsonPath = '$.' . $locale;
            $query->orWhere(
                DB::raw("LOWER({$field}->'{$jsonPath}')"),
                'like',
                '%' . strtolower($searchTerm) . '%'
            );
        }
    }
}
