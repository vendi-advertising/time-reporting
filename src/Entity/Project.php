<?php

namespace App\Entity;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiEntity([Client::class])]
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
    private ProjectCategory $projectCategory;

    #[ORM\ManyToMany(targetEntity: ProjectTask::class, mappedBy: 'project')]
    private $projectTasks;

    public function __construct(int $id, string $name, ?string $code, ?float $budget, bool $isActive, Client $client)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->budget = $budget;
        $this->isActive = $isActive;
        $this->client = $client;
        $this->users = new ArrayCollection();
        $this->projectTasks = new ArrayCollection();
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

    /**
     * @return Collection|ProjectTask[]
     */
    public function getProjectTasks(): Collection
    {
        return $this->projectTasks;
    }

    public function addProjectTask(ProjectTask $projectTask): self
    {
        if (!$this->projectTasks->contains($projectTask)) {
            $this->projectTasks[] = $projectTask;
            $projectTask->addProject($this);
        }

        return $this;
    }

    public function removeProjectTask(ProjectTask $projectTask): self
    {
        if ($this->projectTasks->removeElement($projectTask)) {
            $projectTask->removeProject($this);
        }

        return $this;
    }
}
