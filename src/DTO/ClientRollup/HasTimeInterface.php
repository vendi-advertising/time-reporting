<?php

namespace App\DTO\ClientRollup;

interface HasTimeInterface
{
    public function hasTime(): bool;

    public function getTime(): float;
}