<?php

namespace App\DTO\Rollup;

use App\Entity\Client;
use App\Entity\Project;

class RollupClient extends AbstractHasTimeObject
{
    /**
     * @var RollupClient[]
     */
    public array $projects = [];

    public function __construct(public readonly int $id, public readonly string $name)
    {
    }

    public static function fromEntity(Client $entity): self
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