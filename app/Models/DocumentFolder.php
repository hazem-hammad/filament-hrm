<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentFolder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'color',
        'is_private',
        'created_by',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Parent folder relationship
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'parent_id');
    }

    /**
     * Child folders relationship
     */
    public function children(): HasMany
    {
        return $this->hasMany(DocumentFolder::class, 'parent_id')
            ->orderBy('name');
    }

    /**
     * All descendant folders (recursive)
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Documents in this folder
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'folder_id')
            ->orderBy('name');
    }

    /**
     * Admin who created the folder
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get the folder path (breadcrumb)
     */
    public function getPathAttribute(): array
    {
        $path = [];
        $folder = $this;
        
        while ($folder) {
            array_unshift($path, [
                'id' => $folder->id,
                'name' => $folder->name,
                'color' => $folder->color,
            ]);
            $folder = $folder->parent;
        }
        
        return $path;
    }

    /**
     * Get total size of folder (including subfolders)
     */
    public function getTotalSizeAttribute(): int
    {
        $size = $this->documents()->sum('file_size');
        
        if ($this->relationLoaded('children')) {
            foreach ($this->children as $child) {
                $size += $child->total_size;
            }
        } else {
            $children = $this->children()->with('children')->get();
            foreach ($children as $child) {
                $size += $child->total_size;
            }
        }
        
        return $size;
    }

    /**
     * Get total file count (including subfolders)
     */
    public function getTotalFileCountAttribute(): int
    {
        $count = $this->documents()->count();
        
        if ($this->relationLoaded('children')) {
            foreach ($this->children as $child) {
                $count += $child->total_file_count;
            }
        } else {
            $children = $this->children()->with('children')->get();
            foreach ($children as $child) {
                $count += $child->total_file_count;
            }
        }
        
        return $count;
    }

    /**
     * Get direct children count (folders + files)
     */
    public function getItemCountAttribute(): int
    {
        return $this->children()->count() + $this->documents()->count();
    }

    /**
     * Check if folder is root (no parent)
     */
    public function getIsRootAttribute(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Get human readable size
     */
    public function getFormattedSizeAttribute(): string
    {
        return $this->formatBytes($this->total_size);
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
     * Scope to get root folders only
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get public folders
     */
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // When deleting a folder, move its contents to parent or delete
        static::deleting(function ($folder) {
            // Move child folders to parent
            $folder->children()->update(['parent_id' => $folder->parent_id]);

            // Move documents to parent folder
            $folder->documents()->update(['folder_id' => $folder->parent_id]);
        });
    }
}