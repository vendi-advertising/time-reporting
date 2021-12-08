<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class HttpRequestItemCountUpdatedEvent extends Event
{
    public int $currentItemCount;
    public string $url;
    public array $options;

    public function __construct(int $currentItemCount, string $url, array $options)
    {
        $this->currentItemCount = $currentItemCount;
        $this->url = $url;
        $this->options = $options;
    }
}