<?php

namespace App\Service\Fetchers;

use App\Entity\TimeEntry;
use App\Repository\TimeEntryRepository;
use DateTimeImmutable;
use DateTimeInterface;

class TimeEntryFetcher extends AbstractSimpleFetcher
{
    public function __construct(TimeEntryRepository $timeEntryRepository)
    {
        parent::__construct(
            '/v2/time_entries',
            'time_entries',
            $timeEntryRepository,
            TimeEntry::class,
            options: [
                'query' => [
                    'from' => (new DateTimeImmutable('now -1 year'))->format(DateTimeInterface::ATOM),
                ],
            ],
        );
    }
}