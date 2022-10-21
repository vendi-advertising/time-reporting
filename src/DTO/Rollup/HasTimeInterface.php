<?php

namespace App\DTO\Rollup;

interface HasTimeInterface
{
    public function hasTime(): bool;

    public function getTime(): float;
}