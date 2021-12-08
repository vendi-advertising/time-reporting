<?php

namespace App\Command\Harvest\Import;

use App\Events\HttpRequestItemCountDeterminedEvent;
use App\Events\HttpRequestItemCountUpdatedEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractCommandWithHttpRequestProgressBar extends Command
{

    private EventDispatcherInterface $eventDispatcher;

    #[Required]
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function setupHttpRequestProgressBar(SymfonyStyle $io): void
    {
        // TODO: Unfortunately the total items doesn't map correctly when using the update_since parameter, apparently
        $progressBar = null;
        $this->eventDispatcher->addListener(
            HttpRequestItemCountDeterminedEvent::class,
            function (HttpRequestItemCountDeterminedEvent $event) use ($io, &$progressBar) {
                $progressBar = $io->createProgressBar($event->totalItemCount);
                $progressBar->start();
            }
        );
        $this->eventDispatcher->addListener(
            HttpRequestItemCountUpdatedEvent::class,
            function (HttpRequestItemCountUpdatedEvent $event) use (&$progressBar) {
                $progressBar?->setProgress($event->currentItemCount);
            }
        );
    }
}