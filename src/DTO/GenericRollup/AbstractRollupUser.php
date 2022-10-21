<?php

namespace App\DTO\GenericRollup;

use App\DTO\ClientRollup\AbstractHasTimeObject;
use App\Entity\User;

abstract class AbstractRollupUser extends AbstractHasTimeObject
{
    use MakeUserEntityTrait;

    final public function __construct(public readonly int $id, public readonly User $user)
    {
    }
}