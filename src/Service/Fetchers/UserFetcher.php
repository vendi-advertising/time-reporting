<?php

namespace App\Service\Fetchers;

use App\DTO\HarvestTokens;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ApiEntityMaker;
use App\Service\HarvestApiFetcher;
use Doctrine\ORM\EntityManagerInterface;

final class UserFetcher extends AbstractUpdatedSinceFetcher
{

    private UserRepository $userRepository;

    public function __construct(HarvestApiFetcher $fetcher, EntityManagerInterface $manager, ApiEntityMaker $entityMaker, UserRepository $userRepository)
    {
        parent::__construct($fetcher, $manager, $entityMaker);
        $this->userRepository = $userRepository;
    }

    public function load(): void
    {
        $this->loadThings(
            fn() => $this->fetch(),
            fn() => $this->userRepository->findAll()
        );
    }

    public function fetch(): array
    {
        return $this->getThings('/v2/users', 'users', fn($payload) => $this->transform($payload));
    }

    public function transform(array $payload): User
    {
        return $this->getEntityMaker()->createEntityFromApiPayload(User::class, $payload);
    }

    public function getUserByToken(HarvestTokens $harvestTokens): ?User
    {
        $response = $this->getFetcher()->getHarvestClient()->request(
            'GET',
            '/v2/users/me',
            [
                'headers' => [
                    'Authorization' => "Bearer {$harvestTokens->accessToken}",
                ],
            ]
        );
        $responseArray = $response->toArray();

        $userFromHarvest = $this->transform($responseArray);
        $userFromDatabase = $this->userRepository->find($userFromHarvest->getId());

        // TODO: How can this happen?
        if (!$userFromDatabase) {
            return null;
        }

        $this->getEntityMaker()->mapApiEntityToLocalEntity($userFromHarvest, $userFromDatabase);

        $userFromDatabase->fixRoles();

        $this->getManager()->persist($userFromDatabase);
        $this->getManager()->flush();

        return $userFromDatabase;
    }
}