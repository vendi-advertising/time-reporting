<?php

namespace App\DTO\GenericRollup;

use App\DTO\ClientRollup\AbstractHasTimeObject;

abstract class AbstractRollupProject extends AbstractHasTimeObject
{
    use MakeProjectEntityTrait;

    final public function __construct(public readonly int $id, public readonly string $name)
    {
    }
}