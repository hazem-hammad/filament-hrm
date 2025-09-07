<?php

namespace App\Services;

use App\Models\QRCode;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
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

        // Parse colors
        $bgColor = $this->parseHexColor($qrCode->background_color);
        $fgColor = $this->parseHexColor($qrCode->foreground_color);

        // Generate base QR code with customizations
        $generator = QrCodeGenerator::format('png')
            ->size($qrCode->size ?? 300)
            ->margin($qrCode->margin ?? 1)
            ->backgroundColor($bgColor['r'], $bgColor['g'], $bgColor['b'])
            ->color($fgColor['r'], $fgColor['g'], $fgColor['b'])
            ->errorCorrection($qrCode->error_correction ?? 'M');

        // Add encoding if specified
        if ($qrCode->encoding) {
            $generator->encoding($qrCode->encoding);
        }

        // Generate base QR code image
        $qrCodeImage = $generator->generate($vCardData);

        // Apply advanced customizations if needed
        $finalImage = $this->needsAdvancedProcessing($qrCode)
            ? $this->applyAdvancedCustomizations($qrCodeImage, $qrCode)
            : $qrCodeImage;

        // Store the final QR code image
        Storage::disk('public')->put($filename, $finalImage);

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

    protected function needsAdvancedProcessing(QRCode $qrCode): bool
    {
        return $qrCode->style !== 'square'
            || ($qrCode->gradient_enabled && $qrCode->gradient_start_color && $qrCode->gradient_end_color)
            || ($qrCode->logo_path && Storage::disk('public')->exists($qrCode->logo_path));
    }

    protected function applyAdvancedCustomizations(string $qrCodeImage, QRCode $qrCode): string
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($qrCodeImage);

            // Add logo if provided (simplified version)
            if ($qrCode->logo_path && Storage::disk('public')->exists($qrCode->logo_path)) {
                $image = $this->addLogo($image, $qrCode);
            }

            return $image->toPng();
        } catch (\Exception $e) {
            // If advanced processing fails, return original image
            return $qrCodeImage;
        }
    }

    protected function applyGradient($image, QRCode $qrCode)
    {
        $width = $image->width();
        $height = $image->height();

        // Create gradient overlay
        $gradient = Image::create($width, $height);

        $startColor = $this->parseHexColor($qrCode->gradient_start_color);
        $endColor = $this->parseHexColor($qrCode->gradient_end_color);

        if ($qrCode->gradient_type === 'linear') {
            // Create linear gradient
            for ($y = 0; $y < $height; $y++) {
                $ratio = $y / $height;
                $r = (int)($startColor['r'] * (1 - $ratio) + $endColor['r'] * $ratio);
                $g = (int)($startColor['g'] * (1 - $ratio) + $endColor['g'] * $ratio);
                $b = (int)($startColor['b'] * (1 - $ratio) + $endColor['b'] * $ratio);

                $gradient->drawRectangle(0, $y, $width, $y + 1, function ($rectangle) use ($r, $g, $b) {
                    $rectangle->background(sprintf('rgb(%d,%d,%d)', $r, $g, $b));
                });
            }
        } else {
            // Create radial gradient
            $centerX = $width / 2;
            $centerY = $height / 2;
            $maxRadius = sqrt($centerX * $centerX + $centerY * $centerY);

            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    $distance = sqrt(($x - $centerX) ** 2 + ($y - $centerY) ** 2);
                    $ratio = min($distance / $maxRadius, 1);

                    $r = (int)($startColor['r'] * (1 - $ratio) + $endColor['r'] * $ratio);
                    $g = (int)($startColor['g'] * (1 - $ratio) + $endColor['g'] * $ratio);
                    $b = (int)($startColor['b'] * (1 - $ratio) + $endColor['b'] * $ratio);

                    $gradient->drawPixel($x, $y, sprintf('rgb(%d,%d,%d)', $r, $g, $b));
                }
            }
        }

        // Apply gradient with blend mode
        return $image->place($gradient, 'top-left', 0, 0, 50); // 50% opacity
    }

    protected function applyShape($image, string $style)
    {
        switch ($style) {
            case 'circle':
                // Create circular crop using available methods
                $size = min($image->width(), $image->height());
                return $image->crop($size, $size)->resize($size, $size);

            case 'rounded':
                // For now, return image as-is (rounded corners require complex processing)
                return $image;

            default:
                return $image;
        }
    }

    protected function addLogo($image, QRCode $qrCode)
    {
        $logoPath = Storage::disk('public')->path($qrCode->logo_path);

        if (!file_exists($logoPath)) {
            return $image;
        }

        try {
            $manager = new ImageManager(new Driver());
            $logo = $manager->read($logoPath);

            // Resize logo based on logo_size while maintaining aspect ratio
            $logoSize = $qrCode->logo_size ?? 60;
            $logo->scale(width: $logoSize);

            // Calculate center position
            $imageWidth = $image->width();
            $imageHeight = $image->height();
            $logoWidth = $logo->width();
            $logoHeight = $logo->height();

            $x = ($imageWidth - $logoWidth) / 2;
            $y = ($imageHeight - $logoHeight) / 2;

            // Place logo on QR code
            return $image->place($logo, 'top-left', (int)$x, (int)$y);
        } catch (\Exception $e) {
            // If logo processing fails, return original image
            return $image;
        }
    }

    protected function calculateLogoPosition($image, $logo, string $position): array
    {
        $imageWidth = $image->width();
        $imageHeight = $image->height();
        $logoWidth = $logo->width();
        $logoHeight = $logo->height();

        switch ($position) {
            case 'center':
                return [
                    'x' => ($imageWidth - $logoWidth) / 2,
                    'y' => ($imageHeight - $logoHeight) / 2,
                ];

            case 'top':
                return [
                    'x' => ($imageWidth - $logoWidth) / 2,
                    'y' => 20,
                ];

            case 'bottom':
                return [
                    'x' => ($imageWidth - $logoWidth) / 2,
                    'y' => $imageHeight - $logoHeight - 20,
                ];

            default:
                return [
                    'x' => ($imageWidth - $logoWidth) / 2,
                    'y' => ($imageHeight - $logoHeight) / 2,
                ];
        }
    }

    protected function parseHexColor(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    public function getStyleOptions(): array
    {
        return [
            'square' => 'Square',
            'circle' => 'Circle',
            'rounded' => 'Rounded Corners',
        ];
    }

    public function getEyeStyleOptions(): array
    {
        return [
            'square' => 'Square',
            'circle' => 'Circle',
            'rounded' => 'Rounded',
        ];
    }

    public function getGradientTypeOptions(): array
    {
        return [
            'linear' => 'Linear',
            'radial' => 'Radial',
        ];
    }

    public function getLogoPositionOptions(): array
    {
        return [
            'center' => 'Center',
            'top' => 'Top',
            'bottom' => 'Bottom',
        ];
    }

    public function getErrorCorrectionOptions(): array
    {
        return [
            'L' => 'Low (~7%)',
            'M' => 'Medium (~15%)',
            'Q' => 'Quartile (~25%)',
            'H' => 'High (~30%)',
        ];
    }
}
