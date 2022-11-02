<?php

namespace App\DTO\UserRollup;

use App\DTO\GenericRollup\RollupReportInterface;
use App\Entity\UserTimeEntry;

class RollupReportByUser implements RollupReportInterface
{
    /**
     * @var RollupUser[]
     */
    public array $users = [];

    /**
     * @param UserTimeEntry[] $userTimeEntries
     */
    public function setItems(array $userTimeEntries): void
    {
        foreach ($userTimeEntries as $userTimeEntry) {
            $clientId = $userTimeEntry->getProject()->getClient()->getId();
            $projectId = $userTimeEntry->getProject()->getId();
            $userId = $userTimeEntry->getUser()->getId();

            if (!isset($this->users[$userId])) {
                $this->users[$userId] = RollupUser::fromEntity($userTimeEntry->getUser());
            }
            $rollupUser = $this->users[$userId];

            /** @noinspection DuplicatedCode */
            if (!isset($rollupUser->clients[$clientId])) {
                $rollupUser->clients[$clientId] = RollupClient::fromEntity($userTimeEntry->getProject()->getClient());
            }
            $rollupClient = $rollupUser->clients[$clientId];

            if (!isset($rollupClient->projects[$projectId])) {
                $rollupClient->projects[$projectId] = RollupProject::fromEntity($userTimeEntry->getProject());
            }
            $rollupProject = $rollupClient->projects[$projectId];

            $rollupProject->time += $userTimeEntry->getHours();
        }
    }
}