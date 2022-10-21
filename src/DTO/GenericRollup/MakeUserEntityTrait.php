<?php

namespace App\DTO\GenericRollup;

use App\Entity\User;

trait MakeUserEntityTrait
{
    public static function fromEntity(User $entity): static
    {
        return new static($entity->getId(), $entity);
    }
}