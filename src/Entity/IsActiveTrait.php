<?php

namespace App\Entity;

use App\Attributes\ApiProperty;
use Doctrine\ORM\Mapping as ORM;

trait IsActiveTrait
{
    #[ORM\Column(type: "boolean")]
    #[ApiProperty('is_active')]
    protected bool $isActive;

    final public function getIsActive(): bool
    {
        return $this->isActive;
    }

    final public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}