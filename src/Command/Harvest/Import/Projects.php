<?php

namespace App\Command\Harvest\Import;

use App\Service\Fetchers\ProjectFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:harvest:import:projects', description: 'Import projects from Harvest')]
class Projects extends AbstractCommandWithHttpRequestProgressBar
{

    public function __construct(private readonly ProjectFetcher $fetcher)
    {
        parent::__construct();
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
