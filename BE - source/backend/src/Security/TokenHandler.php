<?php

namespace App\Security;

use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenHandler implements AccessTokenHandlerInterface
{
    private string $jwtSecret;
    
    public function __construct(string $jwtSecret)
    {
        $this->jwtSecret = $jwtSecret;
    }
    
    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        try {
            $decoded = JWT::decode( $accessToken, new Key($this->jwtSecret, 'HS256')
            );
            
            if (!isset($decoded->data->email)) {
                throw new BadCredentialsException('Invalid token: missing email claim');
            }
            
            return new UserBadge($decoded->data->email);
            
        } catch (\Exception $exception) {
            throw new BadCredentialsException('Invalid token: ' . $exception->getMessage());
        }
    }
}
