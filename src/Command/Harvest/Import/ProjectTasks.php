<?php

namespace App\Command\Harvest\Import;

use App\Exception\TimeReportingException;
use App\Service\Fetchers\ProjectTaskFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:harvest:import:project-tasks', description: 'Import project tasks from Harvest')]
class ProjectTasks extends AbstractCommandWithHttpRequestProgressBar
{

    private ProjectTaskFetcher $fetcher;

    public function __construct(ProjectTaskFetcher $fetcher)
    {
        throw new TimeReportingException('Importing of project tasks is a feature that might be dropped because it isn\'t needed');
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