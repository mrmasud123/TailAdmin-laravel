<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use Exception;

class SsoTokenVerifier
{
    /**
     * @throws Exception
     */
    public function verify(string $token): object
    {
        $publicKey = file_get_contents(config('sso.public_key_path'));

        $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

        if ($decoded->iss !== config('sso.expected_issuer')) {
            throw new Exception('Invalid token issuer.');
        }

        if ($decoded->aud !== config('sso.expected_audience')) {
            throw new Exception('Invalid token audience.');
        }

        $cacheKey = 'sso_used_jti_' . $decoded->jti;

        if (Cache::has($cacheKey)) {
            throw new Exception('Token already used.');
        }

        // Mark as used - replay protection
        Cache::put($cacheKey, true, 120);

        return $decoded;
    }
}
