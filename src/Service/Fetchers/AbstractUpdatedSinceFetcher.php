<?php

namespace App\Service\Fetchers;

use App\Entity\SyncLog;
use App\Repository\SyncLogRepository;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractUpdatedSinceFetcher extends AbstractFetcher implements UpdatedSinceInterface
{
    private SyncLogRepository $syncLogRepository;
    private EntityManagerInterface $entityManager;

    public function setLastSync(DateTimeInterface $dateTimeRun = null): void
    {
        $entry = new SyncLog($this->getSyncEntityIdentifier(), $dateTimeRun ?? new DateTimeImmutable(timezone: new DateTimeZone('UTC')));
        $this->entityManager->persist($entry);
        $this->entityManager->flush();
    }

    public function getLastSync(): ?DateTimeInterface
    {
        $ret = $this->syncLogRepository->findOneBy(['entity' => $this->getSyncEntityIdentifier()], ['dateTimeRun' => 'DESC']);

        return $ret?->getDateTimeRun();
    }

    public function getSyncEntityIdentifier(): string
    {
        return static::class;
    }

    #[Required]
    public function setSyncLogRepository(SyncLogRepository $syncLogRepository): void
    {
        $this->syncLogRepository = $syncLogRepository;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    protected function getThings(string $url, string $key, callable $transformer, array $options = [], int $perPage = 100): array
    {
        $lastSync = $this->getLastSync();
        if ($lastSync) {
            $query = [
                'updated_since' => $lastSync->format(DateTimeInterface::ATOM),
            ];
            $options = array_merge_recursive($options, ['query' => $query]);
        }

        return parent::getThings($url, $key, $transformer, $options, $perPage);
    }
}