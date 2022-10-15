<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ApiPropertyEntity extends ApiProperty
{
    public const PROPERTY_TYPE_ENTITY = 'entity';

    public function __construct(private string $entityClass, string $apiArrayKeyName = null)
    {
        parent::__construct($apiArrayKeyName, self::PROPERTY_TYPE_ENTITY);
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}