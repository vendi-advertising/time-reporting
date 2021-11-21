<?php

namespace App\Service;

use App\DTO\HarvestTokens;
use App\Exception\InvalidAuthorizationException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HarvestOauth
{

    private HttpClientInterface $httpClient;
    private ParameterBagInterface $parameterBag;
    private UserRepository $userRepository;
    private EntityManagerInterface $manager;

    public function __construct(HttpClientInterface $harvestUserOauth, ParameterBagInterface $parameterBag, UserRepository $userRepository, EntityManagerInterface $manager)
    {
        $this->httpClient = $harvestUserOauth;
        $this->parameterBag = $parameterBag;
        $this->userRepository = $userRepository;
        $this->manager = $manager;
    }

    public function getUserId(HarvestTokens $harvestTokens): ?int
    {
        $response = $this->httpClient->request(
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

    public function getTokens(string $authorizationCode): HarvestTokens
    {
        $response = $this->httpClient->request(
            'POST',
            '/api/v2/oauth2/token',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => [
                    'code' => $authorizationCode,
                    'client_id' => $this->parameterBag->get('app.harvest.oauth.client_id'),
                    'client_secret' => $this->parameterBag->get('app.harvest.oauth.client_secret'),
                    'grant_type' => 'authorization_code',
                ],
            ]
        );

        return HarvestTokens::fromApiResponse($response->toArray());
    }

    /*
curl -X POST \
  -H "User-Agent: MyApp (yourname@example.com)" \
  -d "code=$AUTHORIZATION_CODE" \
  -d "client_id=$CLIENT_ID" \
  -d "client_secret=$CLIENT_SECRET" \
  -d "grant_type=authorization_code" \
  'https://id.getharvest.com/api/v2/oauth2/token'
     */
}