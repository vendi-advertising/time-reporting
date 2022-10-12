<?php

namespace App\DTO;

class DayOfWeek
{
    public function __construct(
        public readonly string $name,
        public readonly string $displayDate,
        public readonly string $idDate,
    ) {
    }
}