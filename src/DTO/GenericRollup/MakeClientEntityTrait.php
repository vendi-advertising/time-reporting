<?php

namespace App\DTO\GenericRollup;

use App\Entity\Client;

trait MakeClientEntityTrait
{
    public static function fromEntity(Client $entity): static
    {
        return new static($entity->getId(), $entity->getName());
    }
}