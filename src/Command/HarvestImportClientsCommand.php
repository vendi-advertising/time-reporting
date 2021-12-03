<?php

namespace App\Command;

use App\Service\Fetchers\ClientFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:harvest:import:clients', description: 'Import clients from Harvest')]
class HarvestImportClientsCommand extends Command
{
    private ClientFetcher $clientFetcher;

    public function __construct(ClientFetcher $clientFetcher)
    {
        parent::__construct();
        $this->clientFetcher = $clientFetcher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->clientFetcher->load();
        $io->success('Done');

        return Command::SUCCESS;
    }
}
