<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ApiEntity
{
    private array $dependsOn;

    public function __construct(array $dependsOn = [])
    {
        $this->dependsOn = $dependsOn;
    }

    public function getDependsOn(): array
    {
        return $this->dependsOn;
    }
}