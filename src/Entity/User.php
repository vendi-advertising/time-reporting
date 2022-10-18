<?php

namespace App\Entity;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;
use App\DTO\HarvestTokens;
use App\Repository\UserRepository;
use App\Service\Fetchers\UserFetcher;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiEntity(fetcher: UserFetcher::class)]
class User implements UserInterface
{
    use ExternalEntityIdTrait;
    use IsActiveTrait;

    #[ORM\Column]
    #[ApiProperty('first_name')]
    private string $firstName;

    #[ORM\Column]
    #[ApiProperty('last_name')]
    private string $lastName;

    #[ORM\Column(unique: true)]
    #[ApiProperty]
    private string $email;

    #[ORM\Column]
    #[ApiProperty('is_admin')]
    private bool $isAdmin;

    #[ORM\Column]
    #[ApiProperty('is_project_manager')]
    private bool $isProjectManager;

    #[ORM\Column]
    #[ApiProperty('is_contractor')]
    private bool $isContractor;

    #[ORM\Column]
    #[ApiProperty('weekly_capacity')]
    private int $weeklyCapacity;

    #[ORM\Column(length: 1024, nullable: true)]
    #[ApiProperty('avatar_url')]
    private ?string $avatarUrl = null;

    #[ORM\Column(type: "simple_array", nullable: true)]
    private array $roles = [];

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $harvestAccessToken = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $harvestRefreshToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $harvestAccessTokenExpiration = null;

    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'users')]
    private Collection $projects;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TimeEntry::class)]
    private Collection $timeEntries;

    public function __construct(int $id, string $firstName, string $lastName, string $email)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->projects = new ArrayCollection();
        $this->timeEntries = new ArrayCollection();
    }

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

    public function fixRoles(): void
    {
        if ($this->isAdmin) {
            $this->roles[] = 'ROLE_ADMIN';
        }

        if ($this->isProjectManager) {
            $this->roles[] = 'ROLE_PROJECT_MANAGER';
        }

        $this->roles = array_unique($this->roles);
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getHarvestAccessToken(): ?string
    {
        return $this->harvestAccessToken;
    }

    public function setHarvestAccessToken(?string $harvestAccessToken): self
    {
        $this->harvestAccessToken = $harvestAccessToken;

        return $this;
    }

    public function getHarvestRefreshToken(): ?string
    {
        return $this->harvestRefreshToken;
    }

    public function setHarvestRefreshToken(?string $harvestRefreshToken): self
    {
        $this->harvestRefreshToken = $harvestRefreshToken;

        return $this;
    }

    public function getHarvestAccessTokenExpiration(): ?DateTimeInterface
    {
        return $this->harvestAccessTokenExpiration;
    }

    public function setHarvestAccessTokenExpiration(?DateTimeInterface $harvestAccessTokenExpiration): self
    {
        $this->harvestAccessTokenExpiration = $harvestAccessTokenExpiration;

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
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        $this->projects->removeElement($project);

        return $this;
    }

    public function setAccessTokens(HarvestTokens $tokens): void
    {
        $this->setHarvestAccessToken($tokens->accessToken);
        $this->setHarvestRefreshToken($tokens->refreshToken);
        $this->setHarvestAccessTokenExpiration($tokens->getExpirationDateTime());
    }

    /**
     * @return Collection|TimeEntry[]
     */
    public function getTimeEntries(): Collection
    {
        return $this->timeEntries;
    }

    public function addTimeEntry(TimeEntry $timeEntry): self
    {
        if (!$this->timeEntries->contains($timeEntry)) {
            $this->timeEntries[] = $timeEntry;
            $timeEntry->setUser($this);
        }

        return $this;
    }

    public function removeTimeEntry(TimeEntry $timeEntry): self
    {
        if ($this->timeEntries->removeElement($timeEntry)) {
            // set the owning side to null (unless already changed)
            if ($timeEntry->getUser() === $this) {
                $timeEntry->setUser(null);
            }
        }

        return $this;
    }
}
