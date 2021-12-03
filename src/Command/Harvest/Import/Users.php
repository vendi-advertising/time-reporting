<?php

namespace App\Command\Harvest\Import;

use App\Service\Fetchers\UserFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:harvest:import:users', description: 'Import users from Harvest')]
class Users extends Command
{
    private UserFetcher $fetcher;

    public function __construct(UserFetcher $fetcher)
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