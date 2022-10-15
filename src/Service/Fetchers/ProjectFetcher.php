<?php

namespace App\Service\Fetchers;

use App\Entity\Project;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use Exception;

final class ProjectFetcher extends AbstractUpdatedSinceFetcher
{

    public function __construct(private readonly ClientRepository $clientRepository, private readonly ProjectRepository $projectRepository)
    {
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

    public function fetchAndLoadAsync(): void
    {
        $this->getThingsAsync(
            '/v2/projects',
            'projects',
            fn($payload) => $this->transform($payload),
            fn($remoteThings) => $this->persistRemoteThings(
                $remoteThings,
                fn() => $this->projectRepository->findAll()
            )
        );
    }
}