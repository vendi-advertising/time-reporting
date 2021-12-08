<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ApiPropertyEntity extends ApiProperty
{
    public const PROPERTY_TYPE_ENTITY = 'entity';

    private string $entityClass;

    public function __construct(string $entityClass, string $apiArrayKeyName = null)
    {
        parent::__construct($apiArrayKeyName, self::PROPERTY_TYPE_ENTITY);
        $this->entityClass = $entityClass;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}