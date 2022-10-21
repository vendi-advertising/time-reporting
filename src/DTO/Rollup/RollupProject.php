<?php

namespace App\DTO\Rollup;

use App\Entity\Project;

class RollupProject extends AbstractHasTimeObject
{
    /**
     * @var RollupUser[]
     */
    public array $users = [];

    public function __construct(public readonly int $id, public readonly string $name)
    {
    }

    public static function fromEntity(Project $entity): self
    {
        return new self($entity->getId(), $entity->getName());
    }

    public function getTime(): float
    {
        return $this->getTimeFromChildren($this->users);
    }
}