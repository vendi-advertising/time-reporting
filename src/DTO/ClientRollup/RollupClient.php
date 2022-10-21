<?php

namespace App\DTO\ClientRollup;

use App\DTO\GenericRollup\AbstractRollupClient;
use App\Entity\Client;
use App\Entity\Project;

class RollupClient extends AbstractRollupClient
{
    /**
     * @var RollupClient[]
     */
    public array $projects = [];

    public static function fromEntity(Client $entity): static
    {
        return new self($entity->getId(), $entity->getName());
    }

    public function addProjectForReport(Project $project): void
    {
        if (!isset($this->projects[$project->getId()])) {
            $this->projects[$project->getId()] = $project;
        }
    }

    public function getTime(): float
    {
        return $this->getTimeFromChildren($this->projects);
    }
}