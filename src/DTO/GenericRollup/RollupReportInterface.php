<?php

namespace App\DTO\GenericRollup;

interface RollupReportInterface
{
    public function setItems(array $userTimeEntries): void;
}