<?php

namespace App\Service\Fetchers;

interface FetcherInterface
{
    public function load();

    public function fetch(): array;

    public function transform(array $payload): mixed;
}