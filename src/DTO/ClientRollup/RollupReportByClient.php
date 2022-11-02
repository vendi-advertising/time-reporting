<?php

namespace App\DTO\ClientRollup;

use App\DTO\GenericRollup\RollupReportInterface;
use App\Entity\UserTimeEntry;

class RollupReportByClient implements RollupReportInterface
{
    /**
     * @var RollupClient[]
     */
    public array $clients = [];

    /**
     * @param UserTimeEntry[] $userTimeEntries
     */
    public function setItems(array $userTimeEntries): void
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