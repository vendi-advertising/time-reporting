<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ApiProperty
{
    final public const PROPERTY_TYPE_DEFAULT = 'default';
    final public const PROPERTY_TYPE_STRING = 'string';
    final public const PROPERTY_TYPE_DATE = 'date';
    final public const PROPERTY_TYPE_DATETIME = 'datetime';
    public const PROPERTY_TYPE_ENTITY = 'entity';
    final public const PROPERTY_TYPE_BOOLEAN = 'boolean';

    public function __construct(private readonly ?string $apiArrayKeyName = null, private readonly string $apiPropertyType = self::PROPERTY_TYPE_DEFAULT)
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