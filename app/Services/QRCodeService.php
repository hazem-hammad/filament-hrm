<?php

namespace App\Services;

use App\Models\QRCode;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Str;

class QRCodeService
{
    public function generateQRCode(QRCode $qrCode): string
    {
        // Generate VCard data
        $vCardData = $qrCode->generateVCardData();
        
        // Update the model with VCard data
        $qrCode->update(['vcard_data' => $vCardData]);
        
        // Generate unique filename
        $filename = 'qr-codes/contact-' . Str::slug($qrCode->name) . '-' . $qrCode->id . '.png';
        
        // Generate QR code as PNG
        $qrCodeImage = QrCodeGenerator::format('png')
            ->size(300)
            ->margin(1)
            ->backgroundColor(255, 255, 255)
            ->color(0, 0, 0)
            ->generate($vCardData);
        
        // Store the QR code image
        Storage::disk('public')->put($filename, $qrCodeImage);
        
        // Update the model with file path
        $qrCode->update(['qr_code_path' => $filename]);
        
        return $filename;
    }
    
    public function getQRCodeUrl(QRCode $qrCode): ?string
    {
        if (!$qrCode->qr_code_path) {
            return null;
        }
        
        return Storage::disk('public')->url($qrCode->qr_code_path);
    }
    
    public function downloadQRCode(QRCode $qrCode): ?\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if (!$qrCode->qr_code_path || !Storage::disk('public')->exists($qrCode->qr_code_path)) {
            return null;
        }
        
        $filePath = Storage::disk('public')->path($qrCode->qr_code_path);
        $fileName = 'contact-' . Str::slug($qrCode->name) . '-qrcode.png';
        
        return response()->download($filePath, $fileName);
    }
    
    public function regenerateQRCode(QRCode $qrCode): string
    {
        // Delete old QR code if exists
        if ($qrCode->qr_code_path && Storage::disk('public')->exists($qrCode->qr_code_path)) {
            Storage::disk('public')->delete($qrCode->qr_code_path);
        }
        
        // Generate new QR code
        return $this->generateQRCode($qrCode);
    }
}