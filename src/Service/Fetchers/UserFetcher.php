<?php

namespace App\Service\Fetchers;

use App\DTO\HarvestTokens;
use App\Entity\User;
use App\Repository\UserRepository;

final class UserFetcher extends AbstractSimpleFetcher
{
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct('/v2/users', 'users', $userRepository, User::class);
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
        $userFromDatabase = $this->getObjectRepository()->find($userFromHarvest->getId());

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