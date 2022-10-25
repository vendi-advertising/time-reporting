<?php

namespace App\Entity;

use App\Repository\UserTimeEntryRepository;
use DateTimeImmutable;
use DateTimeInterface;
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

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private float $hours;

    #[ORM\Column]
    private int $entryDateInt;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $comment = null;

    public function __construct(User $user, Project $project, DateTimeImmutable|int $entryDate)
    {
        $this->user = $user;
        $this->project = $project;
        $this->entryDateInt = $entryDate instanceof DateTimeInterface ? $entryDate->format('Ymd') : $entryDate;
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
        return DateTimeImmutable::createFromFormat('Ymd', $this->entryDateInt);
    }

    public function getHours(): float
    {
        return $this->hours;
    }

    public function setHours(float $hours): void
    {
        $this->hours = $hours;
    }

    public function getEntryDateInt(): ?int
    {
        return $this->entryDateInt;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
