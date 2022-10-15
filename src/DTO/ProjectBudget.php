<?php

namespace App\DTO;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;

#[ApiEntity]
class ProjectBudget
{
    #[ApiProperty('client_id')]
    public int $clientId;

    #[ApiProperty('project_id')]
    public int $projectId;

    #[ApiProperty('budget_is_monthly')]
    public bool $budgetIsMonthly;

    #[ApiProperty('budget_by')]
    public string $budgetBy;

    #[ApiProperty('is_active')]
    public bool $isActive;

    #[ApiProperty('budget')]
    public ?float $budgetTotal = null;

    #[ApiProperty('budget_spent')]
    public ?float $budgetSpent = null;

    #[ApiProperty('budget_remaining')]
    public ?float $budgetRemaining = null;
}