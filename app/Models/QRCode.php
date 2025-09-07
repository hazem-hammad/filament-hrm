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
    ];
    
    protected $attributes = [
        'vcard_data' => '',
        'is_active' => true,
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
