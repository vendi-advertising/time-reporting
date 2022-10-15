<?php

namespace App\Command\Harvest\Import;

use App\Service\Fetchers\ProjectBudgetFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:harvest:import:project-budgets', description: 'Import project budget from Harvest')]
class ProjectBudgets extends Command
{

    public function __construct(private ProjectBudgetFetcher $fetcher)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->fetcher->fetchAndLoadAsync();
        $io->success('Done');

        return Command::SUCCESS;
    }
}