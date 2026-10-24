<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QRService
{
    /**
     * Generate an SVG data URI string for a given text payload.
     */
    public static function generateSvgDataUri(string $payload, int $size = 250): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svgContent = $writer->writeString($payload);

        return 'data:image/svg+xml;base64,' . base64_encode($svgContent);
    }

    /**
     * Generate pure SVG XML for download.
     */
    public static function generateSvg(string $payload, int $size = 250): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        return $writer->writeString($payload);
    }

    /**
     * Build the dining table URL.
     */
    public static function buildTableUrl(string $ownerId, int $tableNumber, ?string $requestHost = null): string
    {
        $host = $requestHost ?: config('app.url', 'http://localhost:8000');
        $host = rtrim($host, '/');
        $signature = SecurityService::getTableSignature($ownerId, $tableNumber);

        return "{$host}/order/{$ownerId}/{$tableNumber}?code={$signature}";
    }

    /**
     * Alias for buildTableUrl for compatibility.
     */
    public static function buildOrderUrl(string $ownerId, int $tableNumber, ?string $requestHost = null): string
    {
        return self::buildTableUrl($ownerId, $tableNumber, $requestHost);
    }

    /**
     * Alias for generateSvgDataUri for compatibility.
     */
    public static function generateQRCodeDataURL(string $payload, int $size = 250): string
    {
        return self::generateSvgDataUri($payload, $size);
    }

    /**
     * Build the hotel room guest portal URL.
     */
    public static function buildRoomUrl(string $hotelId, string $roomNumber, ?string $requestHost = null): string
    {
        $host = $requestHost ?: config('app.url', 'http://localhost:8000');
        $host = rtrim($host, '/');
        $signature = SecurityService::getRoomSignature($hotelId, $roomNumber);

        return "{$host}/room/{$hotelId}/{$roomNumber}?code={$signature}";
    }
}
