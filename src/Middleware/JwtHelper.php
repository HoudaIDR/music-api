<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    // au moins 32 caractères, sinon erreur "Provided key is too short"
    private static $secretKey = 'ITcampus-SaintMichel-Annecy-BTS-CIEL-2026';
    private static $algorithm = 'HS256';

    public static function generateToken($data, $expiry = 3600)
    {
        $issuedAt = time();
        $payload = [
            'iat' => $issuedAt,
            'exp' => $issuedAt + $expiry,
            'data' => $data,
        ];
        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    public static function validateToken($token)
    {
        try {
            return JWT::decode($token, new Key(self::$secretKey, self::$algorithm));
        } catch (\Exception $e) {
            return null;
        }
    }
}