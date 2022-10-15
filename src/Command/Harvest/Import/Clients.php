<?php

namespace App\Command\Harvest\Import;

use App\Service\Fetchers\ClientFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:harvest:import:clients', description: 'Import clients from Harvest')]
class Clients extends AbstractCommandWithHttpRequestProgressBar
{
    public function __construct(private readonly ClientFetcher $clientFetcher)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->setupHttpRequestProgressBar($io);
        $this->clientFetcher->fetchAndLoadAsync();
        $io->success('Done');

        return Command::SUCCESS;
    }
}
