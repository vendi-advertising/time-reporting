<?php

namespace App\DTO\GenericRollup;

use App\Entity\Project;

trait MakeProjectEntityTrait
{
    public static function fromEntity(Project $entity): static
    {
        return new static($entity->getId(), $entity->getName(), $entity->getCode());
    }
}