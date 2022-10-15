<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class HttpRequestItemCountDeterminedEvent extends Event
{
    public function __construct(public int $totalItemCount, public string $url, public array $options)
    {
    }
}