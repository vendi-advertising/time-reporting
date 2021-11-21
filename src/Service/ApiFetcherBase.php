<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class ApiFetcherBase
{

    protected HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    protected function getThings(string $url, string $key, callable $transformer, array $options = []): array
    {
        $responses = $this->getPagedResponses($url, $options);
        $things = [];
        foreach ($responses as $response) {
            foreach ($response[$key] as $thing) {
                $things[] = $transformer($thing);
            }
        }

        return $things;
    }

    protected function getPagedResponses(string $url, array $options = []): array
    {
        $page = 1;
        $perPage = 100;

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

            $response = $this->httpClient->request(
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
}