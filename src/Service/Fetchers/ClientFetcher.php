<?php

namespace App\Service\Fetchers;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\ApiEntityMaker;
use App\Service\HarvestApiFetcher;
use Doctrine\ORM\EntityManagerInterface;

final class ClientFetcher extends AbstractUpdatedSinceFetcher
{
    private ClientRepository $clientRepository;

    public function __construct(HarvestApiFetcher $fetcher, EntityManagerInterface $manager, ApiEntityMaker $entityMaker, ClientRepository $clientRepository)
    {
        parent::__construct($fetcher, $manager, $entityMaker);
        $this->clientRepository = $clientRepository;
    }

    public function load(): void
    {
        $this->loadThings(
            fn() => $this->fetch(),
            fn() => $this->clientRepository->findAll()
        );
    }

    public function fetch(): array
    {
        return $this->getThings('/v2/clients', 'clients', fn($payload) => $this->transform($payload));
    }

    public function transform(array $payload): Client
    {
        return $this->getEntityMaker()->createEntityFromApiPayload(Client::class, $payload);
    }
}