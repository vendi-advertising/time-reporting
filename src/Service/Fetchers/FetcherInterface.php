<?php

namespace App\Service\Fetchers;

interface FetcherInterface
{
    public function load(): void;

    public function fetch(): array;

    public function transform(array $payload): mixed;
}