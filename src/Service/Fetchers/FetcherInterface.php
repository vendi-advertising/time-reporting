<?php

namespace App\Service\Fetchers;

interface FetcherInterface
{
    public function transform(array $payload): mixed;

    public function fetchAndLoadAsync(): void;
}