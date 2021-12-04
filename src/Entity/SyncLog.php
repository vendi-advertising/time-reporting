<?php

namespace App\Entity;

use App\Repository\SyncLogRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SyncLogRepository::class)]
class SyncLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $entity;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $dateTimeRun;

    public function __construct(string $entity, DateTimeInterface $dateTimeRun)
    {
        $this->entity = $entity;
        $this->dateTimeRun = $dateTimeRun;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getDateTimeRun(): ?DateTimeInterface
    {
        return $this->dateTimeRun;
    }

    public function setDateTimeRun(DateTimeInterface $dateTimeRun): self
    {
        $this->dateTimeRun = $dateTimeRun;

        return $this;
    }
}
