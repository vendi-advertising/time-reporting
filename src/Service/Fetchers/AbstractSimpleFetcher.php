<?php

namespace App\Service\Fetchers;

use Doctrine\Persistence\ObjectRepository;

abstract class AbstractSimpleFetcher extends AbstractUpdatedSinceFetcher
{
    public function __construct(private string $url, private string $key, private ObjectRepository $objectRepository, private string $className, private array $options = [], private int $perPage = 100)
    {
    }

    final public function transform(array $payload): mixed
    {
        return $this->getEntityMaker()->createEntityFromApiPayload($this->className, $payload);
    }

    final public function fetchAndLoadAsync(): void
    {
        $this->getThingsAsync(
            $this->url,
            $this->key,
            fn($payload) => $this->transform($payload),
            fn($remoteThings) => $this->persistRemoteThings(
                $remoteThings,
                fn() => $this->objectRepository->findAll()
            ),
            options: $this->options,
            perPage: $this->perPage
        );

        $this->setLastSync();
    }

    protected function getObjectRepository(): ObjectRepository
    {
        return $this->objectRepository;
    }
}