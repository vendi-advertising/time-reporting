<?php

namespace App\DTO\Rollup;

use App\Entity\User;

class RollupUser implements HasTimeInterface
{
    public float $time = 0;

    public function __construct(public readonly int $id, public readonly User $user)
    {
    }

    public static function fromEntity(User $entity): self
    {
        return new self($entity->getId(), $entity);
    }

    public function hasTime(): bool
    {
        return $this->getTime() > 0;
    }

    public function getTime(): float
    {
        return $this->time;
    }
}