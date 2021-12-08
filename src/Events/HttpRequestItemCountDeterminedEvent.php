<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class HttpRequestItemCountDeterminedEvent extends Event
{
    public int $totalItemCount;
    public string $url;
    public array $options;

    public function __construct(int $totalItemCount, string $url, array $options)
    {
        $this->totalItemCount = $totalItemCount;
        $this->url = $url;
        $this->options = $options;
    }
}