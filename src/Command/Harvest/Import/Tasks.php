<?php

namespace App\Command\Harvest\Import;

use App\Service\Fetchers\TaskFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:harvest:import:tasks', description: 'Import tasks from Harvest')]
class Tasks extends Command
{
    private TaskFetcher $fetcher;

    public function __construct(TaskFetcher $fetcher)
    {
        parent::__construct();
        $this->fetcher = $fetcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->fetcher->load();
        $io->success('Done');

        return Command::SUCCESS;
    }
}