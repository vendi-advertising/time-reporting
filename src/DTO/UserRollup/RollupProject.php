<?php

namespace App\DTO\UserRollup;

use App\DTO\GenericRollup\AbstractRollupProject;

class RollupProject extends AbstractRollupProject
{
    public float $time = 0;

    public function getTime(): float
    {
        return $this->time;
    }
}