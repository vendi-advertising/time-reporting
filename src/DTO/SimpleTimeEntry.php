<?php

namespace App\DTO;

class SimpleTimeEntry
{
    public function __construct(
        public readonly float $hours,
        public readonly ?string $comment
    ) {
    }
}