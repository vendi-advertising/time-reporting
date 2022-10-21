<?php

namespace App\DTO\Rollup;

use App\Entity\UserTimeEntry;

class RollupReport
{
    /**
     * @var RollupClient[]
     */
    public array $clients = [];

    /**
     * @param UserTimeEntry[] $userTimeEntries
     */
    public function __construct(array $userTimeEntries)
    {
        foreach ($userTimeEntries as $userTimeEntry) {
            $clientId = $userTimeEntry->getProject()->getClient()->getId();

            if (!isset($this->clients[$clientId])) {
                $this->clients[$clientId] = RollupClient::fromEntity($userTimeEntry->getProject()->getClient());
            }

            $rollupClient = $this->clients[$clientId];

            $projectId = $userTimeEntry->getProject()->getId();
            if (!isset($rollupClient->projects[$projectId])) {
                $rollupClient->projects[$projectId] = RollupProject::fromEntity($userTimeEntry->getProject());
            }

            $rollupProject = $rollupClient->projects[$projectId];

            $userId = $userTimeEntry->getUser()->getId();
            if (!isset($rollupProject->users[$userId])) {
                $rollupProject->users[$userId] = RollupUser::fromEntity($userTimeEntry->getUser());
            }

            $rollupUser = $rollupProject->users[$userId];
            $rollupUser->time += $userTimeEntry->getHours();
        }
    }
}