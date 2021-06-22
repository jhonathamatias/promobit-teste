<?php namespace App\Services;

use Firebase\JWT\JWT;

class Auth
{
    public static function createToken(array $payload): string
    {
        return JWT::encode($payload, $_ENV['APP_SECRET']);
    }

    public static function verifyToken(string $token)
    {
        if (empty($token)) {
            throw new \Exception('No token provided');
        }

        $splitToken = explode(" ", $token);

        if (count($splitToken) > 1) {
            return JWT::decode($splitToken[1], $_ENV['APP_SECRET'], ['HS256']);
        }

        throw new \Exception('Token malformatted');
    }
}