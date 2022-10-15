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

    public function __construct(private ?string $apiArrayKeyName = null, private string $apiPropertyType = self::PROPERTY_TYPE_DEFAULT)
    {
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