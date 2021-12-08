<?php

namespace App\Service\Fetchers;

use App\Entity\Client;
use App\Repository\ClientRepository;

final class ClientFetcher extends AbstractSimpleFetcher
{
    public function __construct(ClientRepository $clientRepository)
    {
        parent::__construct('/v2/clients', 'clients', $clientRepository, Client::class);
    }
}