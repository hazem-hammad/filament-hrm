<?php

namespace App\Services\User\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadFileService
{
    public function uploadFile(UploadedFile $file, string $directory = ''): \stdClass
    {
        // Normalize directory name
        $directory = trim($directory, '/');
        $uploadPath = $directory ? 'temps/'.$directory : 'temps';

        // Store file
        $path = $file->store($uploadPath);
        if (! $path) {
            throw new \RuntimeException('File upload failed.');
        }

        return (object) [
            'url' => Storage::disk(config('filesystems.default'))->url($path),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    public function addMedia(Model $model, $path, $collection)
    {
        return $model->addMediaFromDisk($path, config('filesystems.default'))->toMediaCollection($collection);
    }
}
