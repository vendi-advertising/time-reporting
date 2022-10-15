<?php

namespace App\Service\Fetchers;

use Doctrine\Persistence\ObjectRepository;

abstract class AbstractSimpleFetcher extends AbstractUpdatedSinceFetcher
{
    public function __construct(private readonly string $url, private readonly string $key, private readonly ObjectRepository $objectRepository, private readonly string $className, private readonly array $options = [], private readonly int $perPage = 100)
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