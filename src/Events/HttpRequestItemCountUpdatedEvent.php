<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class HttpRequestItemCountUpdatedEvent extends Event
{
    public function __construct(public int $currentItemCount, public string $url, public array $options)
    {
    }
}