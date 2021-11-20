<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ApiEntity
{

    public function __construct()
    {
    }
}