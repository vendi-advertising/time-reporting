<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HarvestApiFetcher
{
    private HttpClientInterface $harvestClient;

    public function __construct(HttpClientInterface $harvestClient)
    {
        $this->harvestClient = $harvestClient;
    }

//    public function getUserAssignmentsByUserId(int $userId): array
//    {
//        return $this->getThings(
//            "/v2/users/{$userId}/project_assignments",
//            'project_assignments',
//            fn($payload) => $payload['project']['id']
//        );
//    }
//
//    protected function getThings(string $url, string $key, callable $transformer, array $options = [], int $perPage = 100): array
//    {
//        $responses = $this->getPagedResponses($url, $perPage, $options);
//        $things = [];
//        foreach ($responses as $response) {
//            foreach ($response[$key] as $thing) {
//                $things[] = $transformer($thing);
//            }
//        }
//
//        return $things;
//    }

    public function getPagedResponses(string $url, int $perPage, array $options = []): array
    {
        $page = 1;

        $hasMorePages = true;
        $responses = [];
        while ($hasMorePages) {
            $defaultOptions = [
                'query' => [
                    'page' => $page,
                    'per_page' => $perPage,
                ],
            ];

            $finalOptions = array_merge_recursive($options, $defaultOptions);

            $response = $this->harvestClient->request(
                'GET',
                $url,
                $finalOptions
            );

            $responseArray = $response->toArray();
            $responses[$page] = $responseArray;
            $nextPage = $responseArray['next_page'] ?? $page;
            $hasMorePages = $nextPage > $page;
            $page = $nextPage;
        }

        return $responses;
    }

    /**
     * @return HttpClientInterface
     */
    public function getHarvestClient(): HttpClientInterface
    {
        return $this->harvestClient;
    }
}