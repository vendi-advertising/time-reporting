<?php

namespace App\Command\Harvest\Import;

use App\Service\Fetchers\TimeEntryFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:harvest:import:time-entries', description: 'Import time entries from Harvest')]
class TimeEntries extends AbstractCommandWithHttpRequestProgressBar
{
    private TimeEntryFetcher $fetcher;

    public function __construct(TimeEntryFetcher $fetcher)
    {
        parent::__construct();
        $this->fetcher = $fetcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->setupHttpRequestProgressBar($io);
        $this->fetcher->fetchAndLoadAsync();
        $io->success('Done');

        return Command::SUCCESS;
    }
}