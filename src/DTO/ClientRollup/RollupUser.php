<?php

namespace App\DTO\ClientRollup;

use App\DTO\GenericRollup\AbstractRollupUser;

class RollupUser extends AbstractRollupUser
{
    public float $time = 0;

    public function getTime(): float
    {
        return $this->time;
    }
}