<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTimeInterface;
use Illuminate\Support\Facades\Hash;

class SecurityService
{
    private static function getSecret(): string
    {
        return config('app.jwt_secret') ?: (config('app.key') ?: 'qrestro-secret-fallback-key-32chars');
    }

    public static function getTableSignature(string $ownerId, int $tableNumber): string
    {
        $secret = self::getSecret();
        return substr(hash_hmac('sha256', "{$ownerId}:{$tableNumber}", $secret), 0, 16);
    }

    public static function getRoomSignature(string $hotelId, string $roomNumber): string
    {
        $secret = self::getSecret();
        return substr(hash_hmac('sha256', "room:{$hotelId}:{$roomNumber}", $secret), 0, 16);
    }

    public static function generateSessionToken(string $entityId, DateTimeInterface $updatedAt): string
    {
        $secret = self::getSecret();
        $payload = [
            'entityId' => $entityId,
            'tableId' => $entityId,
            'tableUpdatedAt' => $updatedAt->getTimestamp() * 1000,
            'exp' => time() + (12 * 3600), // 12 hours
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    public static function verifySessionToken(?string $token, DateTimeInterface $currentUpdatedAt): bool
    {
        if (!$token) {
            return false;
        }

        try {
            $secret = self::getSecret();
            $decoded = (array) JWT::decode($token, new Key($secret, 'HS256'));

            if (!isset($decoded['tableUpdatedAt'])) {
                return false;
            }

            $tokenTime = (int) floor($decoded['tableUpdatedAt'] / 1000);
            $tableTime = (int) $currentUpdatedAt->getTimestamp();

            return $tokenTime === $tableTime;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function generateJwtToken(array $payload): string
    {
        $secret = self::getSecret();
        $tokenData = array_merge($payload, [
            'iat' => time(),
            'exp' => time() + (24 * 3600), // 24 hours
        ]);

        return JWT::encode($tokenData, $secret, 'HS256');
    }

    public static function verifyJwtToken(?string $token): ?array
    {
        if (!$token) {
            return null;
        }

        try {
            $secret = self::getSecret();
            $decoded = (array) JWT::decode($token, new Key($secret, 'HS256'));
            if (!isset($decoded['role'])) {
                $decoded['role'] = 'RESTAURANT_OWNER';
            }
            return $decoded;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    public static function comparePassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
