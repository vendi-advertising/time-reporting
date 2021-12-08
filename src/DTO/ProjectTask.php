<?php

namespace App\DTO;

use App\Attributes\ApiEntity;
use App\Attributes\ApiProperty;

#[ApiEntity]
class ProjectTask
{
    #[ApiProperty('project.id')]
    public int $projectId;

    #[ApiProperty('task.id')]
    public int $taskId;

    #[ApiProperty('is_active')]
    public bool $isActive;
}