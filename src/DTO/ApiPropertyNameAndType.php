<?php

namespace App\DTO;

class ApiPropertyNameAndType
{
    public mixed $name;
    public string $type;
    public ?string $entityClass;

    public function __construct(mixed $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
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