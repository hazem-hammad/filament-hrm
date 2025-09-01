<?php

namespace App\Models;

use App\Enum\AssetCondition;
use App\Enum\AssetStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Asset extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'asset_id',
        'name',
        'category',
        'description',
        'brand',
        'model',
        'serial_number',
        'purchase_cost',
        'purchase_date',
        'warranty_months',
        'warranty_expires_at',
        'condition',
        'status',
        'location',
        'assigned_to',
        'assigned_at',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'purchase_cost' => 'decimal:2',
        'purchase_date' => 'date',
        'warranty_expires_at' => 'date',
        'assigned_at' => 'date',
        'condition' => AssetCondition::class,
        'status' => AssetStatus::class,
        'is_active' => 'boolean',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    public function available(Builder $query): void
    {
        $query->where('status', AssetStatus::AVAILABLE);
    }

    #[Scope]
    public function assigned(Builder $query): void
    {
        $query->where('status', AssetStatus::ASSIGNED);
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/jpg', 'image/png'])
            ->singleFile();

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->onlyFor(['image/jpeg', 'image/jpg', 'image/png']);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            if (empty($asset->asset_id)) {
                $asset->asset_id = static::generateAssetId();
            }

            if ($asset->warranty_months && $asset->purchase_date) {
                $asset->warranty_expires_at = $asset->purchase_date->addMonths((int) $asset->warranty_months);
            }
        });

        static::updating(function ($asset) {
            if ($asset->isDirty(['warranty_months', 'purchase_date']) && $asset->warranty_months && $asset->purchase_date) {
                $asset->warranty_expires_at = $asset->purchase_date->addMonths((int) $asset->warranty_months);
            }
        });
    }

    public static function generateAssetId(): string
    {
        $prefix = 'AST';
        $lastAsset = static::where('asset_id', 'like', $prefix . '%')
            ->orderBy('asset_id', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->asset_id, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function isUnderWarranty(): bool
    {
        return $this->warranty_expires_at && $this->warranty_expires_at->isFuture();
    }

    public function assignTo(Employee $employee): void
    {
        $this->update([
            'assigned_to' => $employee->id,
            'assigned_at' => now(),
            'status' => AssetStatus::ASSIGNED,
        ]);
    }

    public function unassign(): void
    {
        $this->update([
            'assigned_to' => null,
            'assigned_at' => null,
            'status' => AssetStatus::AVAILABLE,
        ]);
    }
}
