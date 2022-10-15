<?php

namespace App\Service;

use App\DTO\HarvestTokens;
use App\Entity\User;
use App\Exception\InvalidAuthorizationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HarvestOauth
{
    public function __construct(private readonly HttpClientInterface $harvestUserOauth, private readonly string $harvestOAuthClientId, private readonly string $harvestOAuthClientSecret)
    {
    }

    public function getUserId(HarvestTokens $harvestTokens): ?int
    {
        $response = $this->harvestUserOauth->request(
            'GET',
            '/api/v2/accounts',
            [
                'headers' => [
                    'Authorization' => "Bearer {$harvestTokens->accessToken}",
                ],
            ]
        );

        $responseAsArray = $response->toArray();
        if (!array_key_exists('user', $responseAsArray)) {
            throw new InvalidAuthorizationException('Unable to find a user with the provided access token');
        }

        return (int)$responseAsArray['user']['id'];
    }

    public function refreshTokens(User $user): HarvestTokens
    {
        $response = $this->harvestUserOauth->request(
            'POST',
            '/api/v2/oauth2/token',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => [
                    'refresh_token' => $user->getHarvestRefreshToken(),
                    'client_id' => $this->harvestOAuthClientId,
                    'client_secret' => $this->harvestOAuthClientSecret,
                    'grant_type' => 'refresh_token',
                ],
            ]
        );

        return HarvestTokens::fromApiResponse($response->toArray());
    }

    public function getTokens(string $authorizationCode): HarvestTokens
    {
        $response = $this->harvestUserOauth->request(
            'POST',
            '/api/v2/oauth2/token',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => [
                    'code' => $authorizationCode,
                    'client_id' => $this->harvestOAuthClientId,
                    'client_secret' => $this->harvestOAuthClientSecret,
                    'grant_type' => 'authorization_code',
                ],
            ]
        );

        return HarvestTokens::fromApiResponse($response->toArray());
    }
}