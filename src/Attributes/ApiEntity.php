<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ApiEntity
{
    private array $dependsOn;
    private ?string $fetcher;

    public function __construct(array $dependsOn = [], string $fetcher = null)
    {
        $this->dependsOn = $dependsOn;
        $this->fetcher = $fetcher;
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