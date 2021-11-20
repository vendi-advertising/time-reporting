<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Project;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Fetcher
{
    private HttpClientInterface $client;
    private ClientRepository $clientRepository;
    private EntityManagerInterface $manager;
    private ProjectRepository $projectRepository;

    public function __construct(HttpClientInterface $harvestClient, ClientRepository $clientRepository, ProjectRepository $projectRepository, EntityManagerInterface $manager)
    {
        $this->client = $harvestClient;
        $this->clientRepository = $clientRepository;
        $this->manager = $manager;
        $this->projectRepository = $projectRepository;
    }

    public function loadProjects(): void
    {
        $this->loadThings(
            [$this, 'getProjects'],
            function () {
                return $this->projectRepository->findAll();
            }
        );
    }

    public function loadClients(): void
    {
        $this->loadThings(
            [$this, 'getClients'],
            function () {
                return $this->clientRepository->findAll();
            }
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

    /**
     * @return Client[]
     */
    public function getClients(): array
    {
        return $this->getThings('/v2/clients', 'clients', [$this, 'transformClient']);
    }

    /**
     * @return Project[]
     */
    public function getProjects(): array
    {
        return $this->getThings('/v2/projects', 'projects', [$this, 'transformProject']);
    }

    private function transformProject(array $payload): Project
    {
        $client = $this->clientRepository->find($payload['client']['id']);
        if (!$client) {
            // TODO: There is enough information in the payload to create a client on the fly
            throw new \Exception('Could not find client');
        }

        return new Project(id: $payload['id'], name: $payload['name'], code: $payload['code'], budget: $payload['budget'], isActive: $payload['is_active'], client: $client);
    }

    private function transformClient(array $payload): Client
    {
        return new Client(id: $payload['id'], name: $payload['name'], isActive: $payload['is_active']);
    }

    private function getThings(string $url, string $key, callable $transformer): array
    {
        $responses = $this->getPagedResponses($url);
        $things = [];
        foreach ($responses as $response) {
            foreach ($response[$key] as $thing) {
                $things[] = $transformer($thing);
            }
        }

        return $things;
    }

    private function getPagedResponses(string $url): array
    {
        $page = 1;
        $perPage = 100;
        $hasMorePages = true;
        $responses = [];
        while ($hasMorePages) {
            $response = $this->client->request(
                'GET',
                $url,
                [
                    'query' => [
                        'page' => $page,
                        'per_page' => $perPage,
                    ],
                ]
            );

            $responseArray = $response->toArray();
            $responses[$page] = $responseArray;
            $nextPage = $responseArray['next_page'] ?? $page;
            $hasMorePages = $nextPage > $page;
            $page = $nextPage;
        }

        return $responses;
    }
}