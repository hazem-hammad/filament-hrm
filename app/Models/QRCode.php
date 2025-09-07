<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class QRCode extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'phone_2',
        'email',
        'website',
        'vcard_data',
        'qr_code_path',
        'is_active',
        // Design customization
        'size',
        'margin',
        'style',
        'eye_style',
        // Color customization
        'background_color',
        'foreground_color',
        'eye_color',
        'gradient_enabled',
        'gradient_start_color',
        'gradient_end_color',
        'gradient_type',
        // Logo customization
        'logo_path',
        'logo_size',
        'logo_position',
        'logo_background',
        'logo_background_color',
        // Advanced options
        'error_correction',
        'encoding',
        'custom_options',
    ];
    
    protected $attributes = [
        'vcard_data' => '',
        'is_active' => true,
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'gradient_enabled' => 'boolean',
        'logo_background' => 'boolean',
        'custom_options' => 'array',
        'size' => 'integer',
        'margin' => 'integer',
        'logo_size' => 'integer',
    ];

    #[Scope]
    public function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function generateVCardData(): string
    {
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        $vcard .= "FN:{$this->name}\r\n";
        $vcard .= "TEL;TYPE=CELL:{$this->phone}\r\n";
        
        if ($this->phone_2) {
            $vcard .= "TEL;TYPE=WORK:{$this->phone_2}\r\n";
        }
        
        $vcard .= "EMAIL:{$this->email}\r\n";
        
        if ($this->website) {
            $vcard .= "URL:{$this->website}\r\n";
        }
        
        $vcard .= "END:VCARD\r\n";
        
        return $vcard;
    }
}
