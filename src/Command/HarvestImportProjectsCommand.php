<?php

namespace App\Command;

use App\Service\HarvestApiFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:harvest:import:projects', description: 'Import projects from Harvest')]
class HarvestImportProjectsCommand extends Command
{
    private HarvestApiFetcher $fetcher;

    public function __construct(HarvestApiFetcher $fetcher)
    {
        parent::__construct();
        $this->fetcher = $fetcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->fetcher->loadProjects();
        $io->success('Done');

        return Command::SUCCESS;
    }
}
