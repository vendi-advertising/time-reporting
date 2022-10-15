<?php

namespace App\DTO;

use App\Exception\InvalidAuthorizationException;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Stringable;

class HarvestTokens implements Stringable
{
    public function __construct(public string $accessToken, public string $refreshToken, public int $expiresIn)
    {
    }

    public static function fromApiResponse(array $response): self
    {
        foreach (['access_token', 'refresh_token', 'expires_in'] as $requiredKey) {
            if (!array_key_exists($requiredKey, $response)) {
                throw new InvalidAuthorizationException();
            }
        }

        return new self(
            accessToken: $response['access_token'],
            refreshToken: $response['refresh_token'],
            expiresIn: (int)$response['expires_in']
        );
    }

    public function getExpirationDateTime(): DateTimeInterface
    {
        return (new DateTimeImmutable())->add(new DateInterval('PT'.$this->expiresIn.'S'));
    }

    public function __toString()
    {
        return "Access: {$this->accessToken}; Refresh: {$this->refreshToken}; Expires: {$this->expiresIn}";
    }
}