<?php

namespace App\Service;

use App\DTO\HarvestTokens;
use App\Entity\Client;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HarvestApiFetcher extends ApiFetcherBase
{
    private ClientRepository $clientRepository;
    private EntityManagerInterface $manager;
    private ProjectRepository $projectRepository;
    private ApiEntityMaker $entityMaker;
    private UserRepository $userRepository;

    public function __construct(HttpClientInterface $harvestClient, ClientRepository $clientRepository, ProjectRepository $projectRepository, EntityManagerInterface $manager, ApiEntityMaker $entityMaker, UserRepository $userRepository)
    {
        parent::__construct($harvestClient);
        $this->clientRepository = $clientRepository;
        $this->manager = $manager;
        $this->projectRepository = $projectRepository;
        $this->entityMaker = $entityMaker;
        $this->userRepository = $userRepository;
    }

    public function loadProjects(): void
    {
        $this->loadThings(
            fn() => $this->getProjects(),
            fn() => $this->projectRepository->findAll()
        );
    }

    public function loadClients(): void
    {
        $this->loadThings(
            fn() => $this->getClients(),
            fn() => $this->clientRepository->findAll()
        );
    }

    private function loadThings(callable $remoteGetter, callable $localGetter,): void
    {
        $allRemoteThings = $remoteGetter();
        $allLocalThings = $localGetter();
        foreach ($allRemoteThings as $remoteThing) {
            $foundLocal = false;
            foreach ($allLocalThings as $localThing) {
                if ($localThing->getId() === $remoteThing->getId()) {
                    $foundLocal = true;
                    break;
                }
            }

            if (!$foundLocal) {
                $this->manager->persist($remoteThing);
            }
        }

        $this->manager->flush();
    }

    public function getUser(HarvestTokens $harvestTokens): ?User
    {
        $response = $this->httpClient->request(
            'GET',
            '/v2/users/me',
            [
                'headers' => [
                    'Authorization' => "Bearer {$harvestTokens->accessToken}",
                ],
            ]
        );
        $responseArray = $response->toArray();

        $userFromHarvest = $this->transformUser($responseArray);
        $userFromDatabase = $this->userRepository->find($userFromHarvest->getId());
        $this->entityMaker->mapApiEntityToLocalEntity($userFromHarvest, $userFromDatabase);

        $this->manager->persist($userFromDatabase);
        $this->manager->flush();

        return $userFromDatabase;
    }

    /**
     * @return Client[]
     */
    public function getClients(): array
    {
        return $this->getThings('/v2/clients', 'clients', fn($payload) => $this->transformClient($payload));
    }

    /**
     * @return Project[]
     * @throws Exception
     */
    public function getProjects(): array
    {
        return $this->getThings('/v2/projects', 'projects', fn($payload) => $this->transformProject($payload));
    }

    private function transformUser(array $payload): User
    {
        return $this->entityMaker->createEntityFromApiPayload(User::class, $payload);
    }

    private function transformProject(array $payload): Project
    {
        $client = $this->clientRepository->find($payload['client']['id']);
        if (!$client) {
            // TODO: There is enough information in the payload to create a client on the fly
            throw new Exception('Could not find client');
        }

        /** @var Project $project */
        $project = $this->entityMaker->createEntityFromApiPayload(Project::class, $payload);
        $project->setClient($client);

        return $project;
    }

    private function transformClient(array $payload): Client
    {
        return $this->entityMaker->createEntityFromApiPayload(Client::class, $payload);
    }
}