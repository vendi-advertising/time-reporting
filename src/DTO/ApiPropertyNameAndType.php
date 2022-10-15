<?php

namespace App\DTO;

class ApiPropertyNameAndType
{
    public ?string $entityClass = null;

    public function __construct(public mixed $name, public string $type)
    {
    }

    public function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }

    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }
}