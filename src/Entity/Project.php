<?php

namespace App\Entity;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;
use App\Repository\ProjectRepository;
use App\Service\Fetchers\ProjectFetcher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiEntity([Client::class], fetcher: ProjectFetcher::class)]
class Project
{
    use ExternalEntityIdTrait;
    use IsActiveTrait;

    #[ORM\Column(type: "string", length: 255)]
    #[ApiProperty]
    private string $name;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[ApiProperty]
    private ?string $code = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: "projects")]
    #[ORM\JoinColumn(nullable: false)]
    private Client $client;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: true)]
    #[ApiProperty]
    private ?float $budget = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'projects')]
    private $users;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[ApiProperty('budget_is_monthly')]
    public ?bool $budgetIsMonthly;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $budgetBy;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: true)]
    public ?float $budgetSpent;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: true)]
    public ?float $budgetRemaining;

    #[ORM\ManyToOne(targetEntity: ProjectCategory::class, inversedBy: 'projects')]
    private ?ProjectCategory $projectCategory = null;

    #[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'projects')]
    private $tasks;
//
//    #[ORM\OneToMany(mappedBy: 'project', targetEntity: TimeEntry::class)]
//    private $timeEntries;

    public function __construct(int $id, string $name, ?string $code, ?float $budget, bool $isActive, Client $client)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->budget = $budget;
        $this->isActive = $isActive;
        $this->client = $client;
        $this->users = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->timeEntries = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

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

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addProject($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeProject($this);
        }

        return $this;
    }

    public function getBudgetSpent(): ?float
    {
        return $this->budgetSpent;
    }

    public function getBudgetRemaining(): ?float
    {
        return $this->budgetRemaining;
    }

    public function setBudgetRemaining(?float $budgetRemaining): void
    {
        $this->budgetRemaining = $budgetRemaining;
    }

    public function setBudgetSpent(?float $budgetSpent): void
    {
        $this->budgetSpent = $budgetSpent;
    }

    public function getBudgetBy(): ?string
    {
        return $this->budgetBy;
    }

    public function setBudgetBy(?string $budgetBy): void
    {
        $this->budgetBy = $budgetBy;
    }

    public function isBudgetIsMonthly(): ?bool
    {
        return $this->budgetIsMonthly;
    }

    public function setBudgetIsMonthly(?bool $budgetIsMonthly): void
    {
        $this->budgetIsMonthly = $budgetIsMonthly;
    }

    public function getProjectCategory(): ?ProjectCategory
    {
        return $this->projectCategory;
    }

    public function setProjectCategory(?ProjectCategory $projectCategory): self
    {
        $this->projectCategory = $projectCategory;

        return $this;
    }

    public function getTaskById(int $id): ?Task
    {
        foreach ($this->getTasks() as $task) {
            if ($task->getId() === $id) {
                return $task;
            }
        }

        return null;
    }

    public function hasTaskById(int $id): bool
    {
        foreach ($this->getTasks() as $task) {
            if ($task->getId() === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        $this->tasks->removeElement($task);

        return $this;
    }

//    /**
//     * @return Collection|TimeEntry[]
//     */
//    public function getTimeEntries(): Collection
//    {
//        return $this->timeEntries;
//    }
//
//    public function addTimeEntry(TimeEntry $timeEntry): self
//    {
//        if (!$this->timeEntries->contains($timeEntry)) {
//            $this->timeEntries[] = $timeEntry;
//            $timeEntry->setProject($this);
//        }
//
//        return $this;
//    }
//
//    public function removeTimeEntry(TimeEntry $timeEntry): self
//    {
//        if ($this->timeEntries->removeElement($timeEntry)) {
//            // set the owning side to null (unless already changed)
//            if ($timeEntry->getProject() === $this) {
//                $timeEntry->setProject(null);
//            }
//        }
//
//        return $this;
//    }
}
