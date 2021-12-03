<?php

namespace App\Command;

use App\Service\Fetchers\ProjectFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:harvest:import:projects', description: 'Import projects from Harvest')]
class HarvestImportProjectsCommand extends Command
{

    private ProjectFetcher $fetcher;

    public function __construct(ProjectFetcher $fetcher)
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
