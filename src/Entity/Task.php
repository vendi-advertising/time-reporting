<?php

namespace App\Entity;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiEntity]
class Task
{
    use ExternalEntityIdTrait;
    use IsActiveTrait;

    #[ORM\Column(type: 'string', length: 255)]
    #[ApiProperty]
    private string $name;

    #[ORM\Column(type: 'boolean')]
    #[ApiProperty('billable_by_default')]
    private bool $isBillableByDefault;

    #[ORM\Column(type: 'float', nullable: true)]
    #[ApiProperty('default_hourly_rate')]
    private ?float $defaultHourlyRate;

    #[ORM\Column(type: 'boolean')]
    #[ApiProperty('is_default')]
    private bool $isDefault = false;

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'tasks')]
    private $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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

    public function getIsBillableByDefault(): ?bool
    {
        return $this->isBillableByDefault;
    }

    public function setIsBillableByDefault(bool $isBillableByDefault): self
    {
        $this->isBillableByDefault = $isBillableByDefault;

        return $this;
    }

    public function getDefaultHourlyRate(): ?float
    {
        return $this->defaultHourlyRate;
    }

    public function setDefaultHourlyRate(float $defaultHourlyRate): self
    {
        $this->defaultHourlyRate = $defaultHourlyRate;

        return $this;
    }

    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addTask($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            $project->removeTask($this);
        }

        return $this;
    }
}
