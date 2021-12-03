<?php

namespace App\Service\Fetchers;

use App\DTO\ProjectBudget;
use App\Repository\ProjectRepository;
use App\Service\ApiEntityMaker;
use App\Service\HarvestApiFetcher;
use Doctrine\ORM\EntityManagerInterface;

final class ProjectBudgetFetcher extends AbstractFetcher
{
    private ProjectRepository $projectRepository;

    public function __construct(HarvestApiFetcher $fetcher, EntityManagerInterface $manager, ApiEntityMaker $entityMaker, ProjectRepository $projectRepository)
    {
        parent::__construct($fetcher, $manager, $entityMaker);
        $this->projectRepository = $projectRepository;
    }

    public function load()
    {
        $projectBudgets = $this->fetch();
        $allProjects = $this->projectRepository->findAll();
        foreach ($projectBudgets as $projectBudget) {
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
    }

    public function fetch(): array
    {
        return $this->getThings('/v2/reports/project_budget', 'results', fn($payload) => $this->transform($payload), perPage: 1000);
    }

    public function transform(array $payload): ProjectBudget
    {
        return $this->getEntityMaker()->createEntityFromApiPayload(ProjectBudget::class, $payload);
    }
}