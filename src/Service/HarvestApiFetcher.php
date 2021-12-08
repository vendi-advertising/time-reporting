<?php

namespace App\Service;

use App\Events\HttpRequestItemCountDeterminedEvent;
use App\Events\HttpRequestItemCountUpdatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HarvestApiFetcher
{
    private HttpClientInterface $harvestClient;
    private LoggerInterface $logger;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(HttpClientInterface $harvestClient, LoggerInterface $logger, EventDispatcherInterface $eventDispatcher)
    {
        $this->harvestClient = $harvestClient;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
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

    public function getSingleResponseArray(string $url, int $perPage, array $options = [], int $page = 1): array
    {
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

        return $response->toArray();
    }

    public function getPagedResponsesAsync(string $url, int $perPage, callable $untransformedGroupedResponseFunction, array $options): void
    {
        $this->logger->debug('Call to getPagedResponsesAndApplyCallback', ['url' => $url, 'per-page' => $perPage, 'options' => $options]);
        $page = 1;

        $hasMorePages = true;
        $itemCountDetermined = false;
        $itemCount = 0;
        while ($hasMorePages) {

            $this->logger->debug('Fetching remote', ['url' => $url, 'per-page' => $perPage, 'page' => $page]);

            $responseArray = $this->getSingleResponseArray($url, $perPage, $options, $page);

            $untransformedGroupedResponseFunction($responseArray, $page);

            $itemCount += count($responseArray);
            $nextPage = $responseArray['next_page'] ?? $page;
            $hasMorePages = $nextPage > $page;
            $page = $nextPage;

            if (!$itemCountDetermined && $responseArray['total_entries']) {
                $this->eventDispatcher->dispatch(new HttpRequestItemCountDeterminedEvent($responseArray['total_entries'], $url, $options));
                $itemCountDetermined = true;
            }

            $this->eventDispatcher->dispatch(new HttpRequestItemCountUpdatedEvent($itemCount, $url, $options));
        }
    }

    /**
     * @deprecated Prefer the async version
     */
    public function getPagedResponsesSync(string $url, int $perPage, array $options): array
    {
        $responses = [];
        $this
            ->getPagedResponsesAsync(
                $url,
                $perPage,
                static function ($responseArray, $page) use (&$responses) {
                    $responses[$page] = $responseArray;
                },
                $options
            );

        return $responses;
    }


    /**
     * @deprecated Prefer the async version
     */
    public function getPagedResponses(string $url, int $perPage, array $options): array
    {
        return $this->getPagedResponsesSync($url, $perPage, $options);
    }

    /**
     * @return HttpClientInterface
     */
    public function getHarvestClient(): HttpClientInterface
    {
        return $this->harvestClient;
    }
}