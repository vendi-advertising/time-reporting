<?php

namespace App\DTO\GenericRollup;

use App\DTO\ClientRollup\AbstractHasTimeObject;

abstract class AbstractRollupClient extends AbstractHasTimeObject
{
    use MakeClientEntityTrait;

    final public function __construct(public readonly int $id, public readonly string $name)
    {
    }
}