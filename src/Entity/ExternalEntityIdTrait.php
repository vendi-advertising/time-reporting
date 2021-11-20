<?php

namespace App\Entity;

use App\Attributes\ApiProperty;
use Doctrine\ORM\Mapping as ORM;

trait ExternalEntityIdTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: "integer")]
    #[ApiProperty]
    protected int $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}