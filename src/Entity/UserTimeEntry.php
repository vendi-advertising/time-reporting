<?php

namespace App\Entity;

use App\Repository\UserTimeEntryRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserTimeEntryRepository::class)]
class UserTimeEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $entryDate;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private float $hours;

    public function __construct(User $user, Project $project, DateTimeImmutable $entryDate)
    {
        $this->user = $user;
        $this->project = $project;
        $this->entryDate = $entryDate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getEntryDate(): DateTimeImmutable
    {
        return $this->entryDate;
    }

    public function getHours(): float
    {
        return $this->hours;
    }

    public function setHours(float $hours): void
    {
        $this->hours = $hours;
    }
}
