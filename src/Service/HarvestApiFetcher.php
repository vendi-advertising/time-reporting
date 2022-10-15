<?php

namespace App\Service;

use App\Events\HttpRequestItemCountDeterminedEvent;
use App\Events\HttpRequestItemCountUpdatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HarvestApiFetcher
{
    public function __construct(
        private readonly HttpClientInterface $harvestClient,
        private readonly LoggerInterface $logger,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

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

    public function getHarvestClient(): HttpClientInterface
    {
        return $this->harvestClient;
    }
}