<?php

namespace App\Service\Fetchers;

use DateTimeInterface;

interface UpdatedSinceInterface
{
    public function getLastSync(): ?DateTimeInterface;
}