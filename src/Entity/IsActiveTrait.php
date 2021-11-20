<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IsActiveTrait
{
    #[ORM\Column(type: "boolean")]
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