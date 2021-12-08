<?php

namespace App\Service\Fetchers;

use App\DTO\ProjectBudget;
use App\Repository\ProjectRepository;

final class ProjectBudgetFetcher extends AbstractFetcher
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function transform(array $payload): ProjectBudget
    {
        return $this->getEntityMaker()->createEntityFromApiPayload(ProjectBudget::class, $payload);
    }

    public function fetchAndLoadAsync(): void
    {
        $this->getThingsAsync(
            '/v2/reports/project_budget',
            'results',
            fn($payload) => $this->transform($payload),
            function ($remoteThings) {
                $allProjects = $this->projectRepository->findAll();
                foreach ($remoteThings as $projectBudget) {
                    foreach ($allProjects as $project) {
                        if ($project->getId() === $projectBudget->projectId) {
                            $project->setBudgetRemaining($projectBudget->budgetRemaining);
                            $project->setBudgetSpent($projectBudget->budgetSpent);
                            $project->setBudgetBy($projectBudget->budgetBy);
                            $project->setBudgetIsMonthly($projectBudget->budgetIsMonthly);
                            $this->getManager()->persist($project);
                            break;
                        }
                    }
                }

                $this->getManager()->flush();
            },
            perPage: 1000
        );
    }
}