<?php

namespace App\Service\Fetchers;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Service\ApiEntityMaker;
use App\Service\HarvestApiFetcher;
use Doctrine\ORM\EntityManagerInterface;

class TaskFetcher extends AbstractUpdatedSinceFetcher
{
    private TaskRepository $taskRepository;

    public function __construct(HarvestApiFetcher $fetcher, EntityManagerInterface $manager, ApiEntityMaker $entityMaker, TaskRepository $taskRepository)
    {
        parent::__construct($fetcher, $manager, $entityMaker);
        $this->taskRepository = $taskRepository;
    }

    public function load(): void
    {
        $this->loadThings(
            fn() => $this->fetch(),
            fn() => $this->taskRepository->findAll()
        );
    }

    public function fetch(): array
    {
        return $this->getThings('/v2/tasks', 'tasks', fn($payload) => $this->transform($payload));
    }

    public function transform(array $payload): Task
    {
        return $this->getEntityMaker()->createEntityFromApiPayload(Task::class, $payload);
    }
}