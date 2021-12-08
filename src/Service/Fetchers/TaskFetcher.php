<?php

namespace App\Service\Fetchers;

use App\Entity\Task;
use App\Repository\TaskRepository;

class TaskFetcher extends AbstractSimpleFetcher
{
    public function __construct(TaskRepository $taskRepository)
    {
        parent::__construct('/v2/tasks', 'tasks', $taskRepository, Task::class);
    }
}