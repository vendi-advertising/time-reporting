<?php

namespace App\Service\Fetchers;

use App\DTO\ProjectTask;
use App\Entity\Project;
use App\Entity\Task;
use App\Repository\ProjectRepository;

class ProjectTaskFetcher extends AbstractUpdatedSinceFetcher
{

    public function __construct(private ProjectRepository $projectRepository)
    {
    }

    public function transform(array $payload): mixed
    {
        return $this->getEntityMaker()->createEntityFromApiPayload(ProjectTask::class, $payload);
    }

    public function fetchAndLoadAsync(): void
    {
        $this->getThingsAsync(
            '/v2/task_assignments',
            'task_assignments',
            fn($payload) => $this->transform($payload),
            function ($remoteThings) {
                /** @var Project[] $allProjects */
                $allProjects = $this->projectRepository->findAllProjectsWithTasks();

                foreach ($remoteThings as $projectTask) {
                    foreach ($allProjects as $project) {
                        if ($projectTask->projectId !== $project->getId()) {
                            continue;
                        }

                        $taskForCurrentProject = $project->getTaskById($projectTask->taskId);
                        if (!$projectTask->isActive && $taskForCurrentProject) {
                            $project->removeTask($taskForCurrentProject);
                        } elseif ($projectTask->isActive && !$taskForCurrentProject) {
                            $newTask = new Task();
                            $project->addTask($taskForCurrentProject);
                        }
                        $this->getManager()->persist($project);
                    }
                }

                $this->getManager()->flush();
            }
        );
    }
}