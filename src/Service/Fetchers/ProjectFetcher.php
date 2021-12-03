<?php

namespace App\Service\Fetchers;

use App\Entity\Project;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use App\Service\ApiEntityMaker;
use App\Service\HarvestApiFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

final class ProjectFetcher extends AbstractFetcher
{

    private ProjectRepository $projectRepository;
    private ClientRepository $clientRepository;

    public function __construct(HarvestApiFetcher $fetcher, EntityManagerInterface $manager, ApiEntityMaker $entityMaker, ClientRepository $clientRepository, ProjectRepository $projectRepository)
    {
        parent::__construct($fetcher, $manager, $entityMaker);
        $this->projectRepository = $projectRepository;
        $this->clientRepository = $clientRepository;
    }

    public function load(): void
    {
        $this->loadThings(
            fn() => $this->fetch(),
            fn() => $this->projectRepository->findAll()
        );
    }

    public function fetch(): array
    {
        return $this->getThings('/v2/projects', 'projects', fn($payload) => $this->transform($payload));
    }

    public function transform(array $payload): Project
    {
        $client = $this->clientRepository->find($payload['client']['id']);
        if (!$client) {
            // TODO: There is enough information in the payload to create a client on the fly
            throw new Exception('Could not find client');
        }

        /** @var Project $project */
        $project = $this->getEntityMaker()->createEntityFromApiPayload(Project::class, $payload);
        $project->setClient($client);

        return $project;
    }
}