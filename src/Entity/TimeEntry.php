<?php

namespace App\Entity;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;
use App\Repository\TimeEntryRepository;
use App\Service\Fetchers\TimeEntryFetcher;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
#[ApiEntity([User::class, Client::class, Project::class], TimeEntryFetcher::class)]
class TimeEntry
{
    use ExternalEntityIdTrait;

    #[ORM\Column(type: 'date_immutable')]
    #[ApiProperty('spent_date', ApiProperty::PROPERTY_TYPE_DATE)]
    private DateTimeInterface $entryDateTime;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty('user.id', ApiProperty::PROPERTY_TYPE_ENTITY)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty('client.id', ApiProperty::PROPERTY_TYPE_ENTITY)]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty('project.id', ApiProperty::PROPERTY_TYPE_ENTITY)]
    private Project $project;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[ApiProperty]
    private float $hours;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[ApiProperty('rounded_hours')]
    private float $hoursRounded;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    #[ApiProperty]
    private ?string $notes = null;

    #[ORM\Column(type: 'boolean')]
    #[ApiProperty('is_billed')]
    private bool $isBilled;

    #[ORM\Column(type: 'boolean')]
    #[ApiProperty('is_closed')]
    private bool $isClosed;

    #[ORM\Column(type: 'boolean')]
    #[ApiProperty('is_running')]
    private bool $isRunning;

    #[ORM\Column(type: 'boolean')]
    #[ApiProperty('billable')]
    private bool $isBillable;

    #[ORM\Column(type: 'boolean')]
    #[ApiProperty('budgeted')]
    private bool $isBudgeted;

    public function getEntryDateTime(): DateTimeInterface
    {
        return $this->entryDateTime;
    }

    public function setEntryDateTime(DateTimeInterface $entryDateTime): self
    {
        $this->entryDateTime = $entryDateTime;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getHours(): float
    {
        return $this->hours;
    }

    public function setHours(float $hours): self
    {
        $this->hours = $hours;

        return $this;
    }

    public function getHoursRounded(): string
    {
        return $this->hoursRounded;
    }

    public function setHoursRounded(string $hoursRounded): self
    {
        $this->hoursRounded = $hoursRounded;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getIsBilled(): bool
    {
        return $this->isBilled;
    }

    public function setIsBilled(bool $isBilled): self
    {
        $this->isBilled = $isBilled;

        return $this;
    }

    public function getIsClosed(): bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): self
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    public function getIsRunning(): bool
    {
        return $this->isRunning;
    }

    public function setIsRunning(bool $isRunning): self
    {
        $this->isRunning = $isRunning;

        return $this;
    }

    public function getIsBillable(): bool
    {
        return $this->isBillable;
    }

    public function setIsBillable(bool $isBillable): self
    {
        $this->isBillable = $isBillable;

        return $this;
    }

    public function getIsBudgeted(): bool
    {
        return $this->isBudgeted;
    }

    public function setIsBudgeted(bool $isBudgeted): self
    {
        $this->isBudgeted = $isBudgeted;

        return $this;
    }
}
