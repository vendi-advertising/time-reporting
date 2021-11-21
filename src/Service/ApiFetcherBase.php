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

    protected function getThings(string $url, string $key, callable $transformer): array
    {
        $responses = $this->getPagedResponses($url);
        $things = [];
        foreach ($responses as $response) {
            foreach ($response[$key] as $thing) {
                $things[] = $transformer($thing);
            }
        }

        return $things;
    }

    protected function getPagedResponses(string $url): array
    {
        $page = 1;
        $perPage = 100;
        $hasMorePages = true;
        $responses = [];
        while ($hasMorePages) {
            $response = $this->httpClient->request(
                'GET',
                $url,
                [
                    'query' => [
                        'page' => $page,
                        'per_page' => $perPage,
                    ],
                ]
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