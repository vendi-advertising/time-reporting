<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\UserTimeEntry;
use App\Repository\UserTimeEntryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    private function loadFromHarvest()
    {
        $commands = [
            'app:harvest:import:clients',
            'app:harvest:import:users',
            'app:harvest:import:users',
            'app:harvest:import:tasks',
            'app:harvest:import:projects',
            'app:harvest:import:project-budgets',
        ];

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        foreach ($commands as $command) {
            $input = new ArrayInput(['command' => $command]);

            // You can use NullOutput() if you don't need the output
            $output = new NullOutput();
            $application->run($input);
        }
    }

    private function addTimeEntries(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $projects = $manager->getRepository(Project::class)->findAll();

        $alwaysProjects = array_rand($projects, 5);

        $entryDate = new \DateTimeImmutable('2022-10-17');

        foreach (array_rand($users, 7) as $userIdx) {
            $user = $users[$userIdx];

            $theseProjectIds = [...$alwaysProjects, ...array_rand($projects, 10)];
            $theseProjects = [];
            foreach ($theseProjectIds as $projectIdx) {
                $theseProjects[] = $projects[$projectIdx];
            }

            foreach ($theseProjects as $project) {
                $timeEntry = new UserTimeEntry($user, $project, $entryDate);
                $timeEntry->setHours(random_int(0, 160) / 4);
                $manager->persist($timeEntry);
            }
        }
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadFromHarvest();
        $this->addTimeEntries($manager);

        $manager->flush();
    }
}
