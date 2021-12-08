<?php

namespace App\Service\Fetchers;

use App\Exception\TimeReportingException;
use App\Service\ApiEntityMaker;
use App\Service\HarvestApiFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractFetcher implements FetcherInterface
{
    private EntityManagerInterface $manager;
    private ApiEntityMaker $entityMaker;
    private HarvestApiFetcher $fetcher;

    protected function getEntityMaker(): ApiEntityMaker
    {
        return $this->entityMaker;
    }

    protected function getManager(): EntityManagerInterface
    {
        return $this->manager;
    }

    protected function getFetcher(): HarvestApiFetcher
    {
        return $this->fetcher;
    }

    #[Required]
    public function setManager(EntityManagerInterface $manager): void
    {
        $this->manager = $manager;
    }

    #[Required]
    public function setEntityMaker(ApiEntityMaker $entityMaker): void
    {
        $this->entityMaker = $entityMaker;
    }

    #[Required]
    public function setFetcher(HarvestApiFetcher $fetcher): void
    {
        $this->fetcher = $fetcher;
    }

    protected function getThingsAsync(string $url, string $key, callable $singleItemTransformerFunction, callable $persistenceFunction, array $options = [], int $perPage = 100): void
    {
        $this->fetcher->getPagedResponsesAsync(
            $url,
            $perPage,
            fn($responseArray, $page) => $persistenceFunction($this->transformPagedResponse($responseArray, $key, $singleItemTransformerFunction)),
            $options
        );
    }

    private function transformPagedResponse(array $response, string $key, callable $transformer): array
    {
        $things = [];
        foreach ($response[$key] as $thing) {
            $things[] = $transformer($thing);
        }

        return $things;
    }

    protected function persistRemoteThings(array $remoteThings, callable $localGetter): void
    {
        $allLocalThings = $localGetter();

        foreach ($remoteThings as $remoteThing) {

            $localToBeUpdated = null;
            foreach ($allLocalThings as $localThing) {
                if ($this->areRemoteAndLocalSame($remoteThing, $localThing)) {
                    $localToBeUpdated = $localThing;
                    break;
                }
            }

            if ($localToBeUpdated) {
                $this->entityMaker->mapApiEntityToLocalEntity($remoteThing, $localToBeUpdated);
                $this->manager->persist($localToBeUpdated);
            } else {
                $this->manager->persist($remoteThing);
            }
        }

        $this->manager->flush();
    }

    protected function areRemoteAndLocalSame(object $remoteThing, object $localThing): bool
    {
        if (!method_exists($remoteThing, 'getId')) {
            throw new TimeReportingException('Could not compare remote object, method getId() not found');
        }

        if (!method_exists($localThing, 'getId')) {
            throw new TimeReportingException('Could not compare local object, method getId() not found');
        }

        return $localThing->getId() === $remoteThing->getId();
    }
}