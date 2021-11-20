<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    use ExternalEntityIdTrait;
    use IsActiveTrait;

    #[ORM\Column(type: "string", length: 255)]
    private string $firstName;

    #[ORM\Column(type: "string", length: 255)]
    private string $lastName;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    #[ORM\Column(type: "boolean")]
    private bool $isAdmin;

    #[ORM\Column(type: "boolean")]
    private bool $isProjectManager;

    #[ORM\Column(type: "integer")]
    private int $weeklyCapacity;

    #[ORM\Column(type: "string", length: 1024, nullable: true)]
    private ?string $avatarUrl = null;

    #[ORM\Column(type: "simple_array", nullable: true)]
    private array $roles = [];

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getIsProjectManager(): bool
    {
        return $this->isProjectManager;
    }

    public function setIsProjectManager(bool $isProjectManager): self
    {
        $this->isProjectManager = $isProjectManager;

        return $this;
    }

    public function getWeeklyCapacity(): int
    {
        return $this->weeklyCapacity;
    }

    public function setWeeklyCapacity(int $weeklyCapacity): self
    {
        $this->weeklyCapacity = $weeklyCapacity;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
