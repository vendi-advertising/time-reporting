<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ApiProperty
{
    private ?string $apiArrayKeyName;

    public function __construct(string $apiArrayKeyName = null)
    {
        $this->apiArrayKeyName = $apiArrayKeyName;
    }

    public function getApiArrayKeyName(): ?string
    {
        return $this->apiArrayKeyName;
    }
}