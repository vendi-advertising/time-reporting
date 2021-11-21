<?php

namespace App\Entity;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ApiEntity]
class Client
{
    use ExternalEntityIdTrait;
    use IsActiveTrait;

    #[ORM\Column(type: "string", length: 255)]
    #[ApiProperty]
    private string $name;

    #[ORM\OneToMany(mappedBy: "client", targetEntity: Project::class)]
    private Collection $projects;

    public function __construct(int $id, string $name, bool $isActive)
    {
        $this->id = $id;
        $this->name = $name;
        $this->isActive = $isActive;
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
            $project->setClient($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getClient() === $this) {
                $project->setClient(null);
            }
        }

        return $this;
    }
}
