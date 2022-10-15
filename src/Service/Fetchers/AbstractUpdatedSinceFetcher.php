<?php

namespace App\Service\Fetchers;

use App\Entity\SyncLog;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractUpdatedSinceFetcher extends AbstractFetcher
{
    private EntityManagerInterface $entityManager;

    final public function setLastSync(DateTimeInterface $dateTimeRun = null): void
    {
        $entry = new SyncLog($this->getSyncEntityIdentifier(), $dateTimeRun ?? new DateTimeImmutable(timezone: new DateTimeZone('UTC')));
        $this->entityManager->persist($entry);
        $this->entityManager->flush();
    }

    final public function getSyncEntityIdentifier(): string
    {
        return static::class;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}