<?php

namespace App\DTO\ClientRollup;

use App\DTO\GenericRollup\AbstractRollupProject;

class RollupProject extends AbstractRollupProject
{
    /**
     * @var RollupUser[]
     */
    public array $users = [];

    public function getTime(): float
    {
        return $this->getTimeFromChildren($this->users);
    }
}