<?php

namespace App\DTO\UserRollup;

use App\DTO\GenericRollup\AbstractRollupUser;

class RollupUser extends AbstractRollupUser
{
    /**
     * @var RollupClient[]
     */
    public array $clients = [];

    public function getTime(): float
    {
        return $this->getTimeFromChildren($this->clients);
    }
}