<?php

namespace App\Service\Fetchers;

use Doctrine\Persistence\ObjectRepository;

abstract class AbstractSimpleFetcher extends AbstractUpdatedSinceFetcher
{
    private string $url;
    private string $key;
    private ObjectRepository $objectRepository;
    private string $className;
    private array $options;
    private int $perPage;

    public function __construct(string $url, string $key, ObjectRepository $objectRepository, string $className, array $options = [], int $perPage = 100)
    {
        $this->url = $url;
        $this->key = $key;
        $this->objectRepository = $objectRepository;
        $this->className = $className;
        $this->options = $options;
        $this->perPage = $perPage;
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
}