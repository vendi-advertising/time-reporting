<?php

namespace App\DTO\UserRollup;

use App\DTO\GenericRollup\AbstractRollupClient;

class RollupClient extends AbstractRollupClient
{
    /**
     * @var RollupProject[]
     */
    public array $projects = [];

    public function getTime(): float
    {
        return $this->getTimeFromChildren($this->projects);
    }
}