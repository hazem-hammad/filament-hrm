<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Document extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'original_filename',
        'description',
        'folder_id',
        'file_type',
        'file_size',
        'mime_type',
        'is_private',
        'assigned_to',
        'created_by',
        'metadata',
        'last_accessed_at',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'metadata' => 'array',
        'last_accessed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Folder relationship
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'folder_id');
    }

    /**
     * Employee assigned to document
     */
    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    /**
     * Admin who created the document
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents')
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'text/csv',
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'video/mp4',
                'video/quicktime',
                'audio/mpeg',
                'audio/wav',
                'application/zip',
                'application/x-rar-compressed',
                'application/json',
                'application/xml',
            ])
            ->singleFile();

        $this->addMediaCollection('thumbnails')
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->singleFile();
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('documents')
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->performOnCollections('documents')
            ->nonQueued();
    }

    /**
     * Get file icon based on file type
     */
    public function getFileIconAttribute(): string
    {
        $iconMap = [
            // Documents
            'pdf' => 'heroicon-o-document-text',
            'doc' => 'heroicon-o-document-text',
            'docx' => 'heroicon-o-document-text',
            'txt' => 'heroicon-o-document-text',
            
            // Spreadsheets
            'xls' => 'heroicon-o-table-cells',
            'xlsx' => 'heroicon-o-table-cells',
            'csv' => 'heroicon-o-table-cells',
            
            // Presentations
            'ppt' => 'heroicon-o-presentation-chart-bar',
            'pptx' => 'heroicon-o-presentation-chart-bar',
            
            // Images
            'jpg' => 'heroicon-o-photo',
            'jpeg' => 'heroicon-o-photo',
            'png' => 'heroicon-o-photo',
            'gif' => 'heroicon-o-photo',
            'webp' => 'heroicon-o-photo',
            'svg' => 'heroicon-o-photo',
            
            // Videos
            'mp4' => 'heroicon-o-video-camera',
            'mov' => 'heroicon-o-video-camera',
            'avi' => 'heroicon-o-video-camera',
            'wmv' => 'heroicon-o-video-camera',
            
            // Audio
            'mp3' => 'heroicon-o-musical-note',
            'wav' => 'heroicon-o-musical-note',
            'flac' => 'heroicon-o-musical-note',
            
            // Archives
            'zip' => 'heroicon-o-archive-box',
            'rar' => 'heroicon-o-archive-box',
            '7z' => 'heroicon-o-archive-box',
            'tar' => 'heroicon-o-archive-box',
            
            // Code
            'js' => 'heroicon-o-code-bracket',
            'html' => 'heroicon-o-code-bracket',
            'css' => 'heroicon-o-code-bracket',
            'php' => 'heroicon-o-code-bracket',
            'py' => 'heroicon-o-code-bracket',
            'json' => 'heroicon-o-code-bracket',
            'xml' => 'heroicon-o-code-bracket',
        ];

        return $iconMap[$this->file_type] ?? 'heroicon-o-document';
    }

    /**
     * Get file color based on file type
     */
    public function getFileColorAttribute(): string
    {
        $colorMap = [
            // Documents - Blue
            'pdf' => 'red',
            'doc' => 'blue',
            'docx' => 'blue',
            'txt' => 'gray',
            
            // Spreadsheets - Green
            'xls' => 'green',
            'xlsx' => 'green',
            'csv' => 'green',
            
            // Presentations - Orange
            'ppt' => 'orange',
            'pptx' => 'orange',
            
            // Images - Purple
            'jpg' => 'purple',
            'jpeg' => 'purple',
            'png' => 'purple',
            'gif' => 'purple',
            'webp' => 'purple',
            'svg' => 'purple',
            
            // Videos - Pink
            'mp4' => 'pink',
            'mov' => 'pink',
            'avi' => 'pink',
            'wmv' => 'pink',
            
            // Audio - Indigo
            'mp3' => 'indigo',
            'wav' => 'indigo',
            'flac' => 'indigo',
            
            // Archives - Yellow
            'zip' => 'yellow',
            'rar' => 'yellow',
            '7z' => 'yellow',
            'tar' => 'yellow',
            
            // Code - Cyan
            'js' => 'cyan',
            'html' => 'cyan',
            'css' => 'cyan',
            'php' => 'cyan',
            'py' => 'cyan',
            'json' => 'cyan',
            'xml' => 'cyan',
        ];

        return $colorMap[$this->file_type] ?? 'gray';
    }

    /**
     * Get human readable file size
     */
    public function getFormattedSizeAttribute(): string
    {
        return $this->formatBytes($this->file_size);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $size, int $precision = 2): string
    {
        if ($size === 0) return '0 B';

        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    /**
     * Get the document's full path
     */
    public function getFullPathAttribute(): string
    {
        $path = '';
        if ($this->folder) {
            $folderPath = collect($this->folder->path)->pluck('name')->implode(' > ');
            $path = $folderPath . ' > ';
        }
        return $path . $this->name;
    }

    /**
     * Check if file can be previewed
     */
    public function getCanPreviewAttribute(): bool
    {
        $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'txt', 'csv'];
        return in_array($this->file_type, $previewableTypes);
    }

    /**
     * Update last accessed timestamp
     */
    public function markAsAccessed(): void
    {
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Scope for assigned documents
     */
    public function scopeAssignedTo($query, $employeeId)
    {
        return $query->where('assigned_to', $employeeId);
    }

    /**
     * Scope for public documents
     */
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Update file information when media is added
        static::created(function ($document) {
            if ($media = $document->getFirstMedia('documents')) {
                $document->update([
                    'file_size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'file_type' => $media->getExtensionAttribute(),
                ]);
            }
        });
    }
}