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
use Doctrine\DBAL\Types\Types;
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

//    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TimeEntry::class)]
//    private Collection $timeEntries;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private array $favoriteProjects = [];

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private array $favoriteClients = [];

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    #[ApiProperty('access_roles')]
    private array $accessRoles = [];

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
        return in_array('administrator', $this->accessRoles, true);
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
        if ($this->getIsAdmin()) {
            $this->roles[] = 'ROLE_ADMIN';
        }

        // TODO
//        if ($this->isProjectManager) {
//            $this->roles[] = 'ROLE_PROJECT_MANAGER';
//        }

        $this->roles = array_unique($this->roles);
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername(): string
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

    /**
     * @return int[]
     */
    public function getFavoriteProjects(): array
    {
        return $this->favoriteProjects;
    }

    /**
     * @return int[]
     */
    public function getFavoriteClients(): array
    {
        return $this->favoriteClients;
    }

    public function removeFavoriteProject(int $id): void
    {
        $this->favoriteProjects = $this->intArray($this->favoriteProjects);
        foreach ($this->favoriteProjects as $idx => $favoriteId) {
            if ($id === $favoriteId) {
                unset($this->favoriteProjects[$idx]);
            }
        }
    }

    public function removeFavoriteClient(int $id): void
    {
        $this->favoriteClients = $this->intArray($this->favoriteClients);
        foreach ($this->favoriteClients as $idx => $favoriteId) {
            if ($id === $favoriteId) {
                unset($this->favoriteClients[$idx]);
            }
        }
    }

    /**
     * @param string[]|int[] $array
     * @return int[]
     */
    private function intArray(array $array): array
    {
        $ret = [];
        foreach ($array as $value) {
            if (!$value) {
                continue;
            }

            $ret[] = (int)$value;
        }

        return array_unique($ret);
    }

    public function addFavoriteProject(int $id): void
    {
        $this->favoriteProjects = $this->intArray($this->favoriteProjects);
        if (!in_array($id, $this->favoriteProjects, true)) {
            $this->favoriteProjects[] = $id;
        }
    }

    public function addFavoriteClient(int $id): void
    {
        $this->favoriteClients = $this->intArray($this->favoriteClients);
        if (!in_array($id, $this->favoriteClients, true)) {
            $this->favoriteClients[] = $id;
        }
    }
}
