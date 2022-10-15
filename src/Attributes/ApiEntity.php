<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ApiEntity
{
    public function __construct(private array $dependsOn = [], private ?string $fetcher = null)
    {
    }

    public function getDependsOn(): array
    {
        return $this->dependsOn;
    }

    public function getFetcher(): ?string
    {
        return $this->fetcher;
    }
}