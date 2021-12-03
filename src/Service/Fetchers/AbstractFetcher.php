<?php

namespace App\Service\Fetchers;

use App\Service\ApiEntityMaker;
use App\Service\HarvestApiFetcher;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractFetcher implements FetcherInterface
{
    private EntityManagerInterface $manager;
    private ApiEntityMaker $entityMaker;
    private HarvestApiFetcher $fetcher;

    public function __construct(HarvestApiFetcher $fetcher, EntityManagerInterface $manager, ApiEntityMaker $entityMaker)
    {
        $this->manager = $manager;
        $this->entityMaker = $entityMaker;
        $this->fetcher = $fetcher;
    }

    public function getEntityMaker(): ApiEntityMaker
    {
        return $this->entityMaker;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getManager(): EntityManagerInterface
    {
        return $this->manager;
    }

    /**
     * @return HarvestApiFetcher
     */
    public function getFetcher(): HarvestApiFetcher
    {
        return $this->fetcher;
    }

    protected function getThings(string $url, string $key, callable $transformer, array $options = [], int $perPage = 100): array
    {
        $responses = $this->fetcher->getPagedResponses($url, $perPage, $options);
        $things = [];
        foreach ($responses as $response) {
            foreach ($response[$key] as $thing) {
                $things[] = $transformer($thing);
            }
        }

        return $things;
    }

    protected function loadThings(callable $remoteGetter, callable $localGetter): void
    {
        $allRemoteThings = $remoteGetter();
        $allLocalThings = $localGetter();

        foreach ($allRemoteThings as $remoteThing) {
            $foundLocal = false;
            foreach ($allLocalThings as $localThing) {
                if ($localThing->getId() === $remoteThing->getId()) {
                    $foundLocal = true;
                    break;
                }
            }

            if (!$foundLocal) {
                $this->manager->persist($remoteThing);
            }
        }

        $this->manager->flush();
    }
}