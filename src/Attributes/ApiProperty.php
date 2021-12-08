<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ApiProperty
{
    public const PROPERTY_TYPE_DEFAULT = 'default';
    public const PROPERTY_TYPE_STRING = 'string';
    public const PROPERTY_TYPE_DATE = 'date';
    public const PROPERTY_TYPE_DATETIME = 'datetime';
    public const PROPERTY_TYPE_ENTITY = 'entity';
    public const PROPERTY_TYPE_BOOLEAN = 'boolean';

    private ?string $apiArrayKeyName;
    private string $apiPropertyType;

    public function __construct(string $apiArrayKeyName = null, string $apiPropertyType = self::PROPERTY_TYPE_DEFAULT)
    {
        $this->apiArrayKeyName = $apiArrayKeyName;
        $this->apiPropertyType = $apiPropertyType;
    }

    public function getApiArrayKeyName(): ?string
    {
        return $this->apiArrayKeyName;
    }

    public function getApiPropertyType(): string
    {
        return $this->apiPropertyType;
    }
}