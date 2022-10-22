<?php

namespace App\DTO\ClientRollup;

use App\Entity\UserTimeEntry;

class RollupReportByClient
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
            $projectId = $userTimeEntry->getProject()->getId();
            $userId = $userTimeEntry->getUser()->getId();

            /** @noinspection DuplicatedCode */
            if (!isset($this->clients[$clientId])) {
                $this->clients[$clientId] = RollupClient::fromEntity($userTimeEntry->getProject()->getClient());
            }
            $rollupClient = $this->clients[$clientId];


            if (!isset($rollupClient->projects[$projectId])) {
                $rollupClient->projects[$projectId] = RollupProject::fromEntity($userTimeEntry->getProject());
            }
            $rollupProject = $rollupClient->projects[$projectId];

            if (!isset($rollupProject->users[$userId])) {
                $rollupProject->users[$userId] = RollupUser::fromEntity($userTimeEntry->getUser());
            }
            $rollupUser = $rollupProject->users[$userId];

            $rollupUser->time += $userTimeEntry->getHours();
        }
    }
}