<?php

namespace App\Entity;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;
use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiEntity]
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

    public function __construct(int $id, string $name, ?string $code, ?float $budget, bool $isActive, Client $client)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->budget = $budget;
        $this->isActive = $isActive;
        $this->client = $client;
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
}
