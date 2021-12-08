<?php

namespace App\Tests\Service;

use App\DTO\ProjectTask;
use App\Entity\Client;
use App\Service\ApiEntityMaker;
use PHPUnit\Framework\TestCase;

class ApiEntityMakerTest extends TestCase
{
    public function test_createEntityFromApiPayload__Simple(): void
    {
        $payload = [
            'id' => 1,
            'name' => 'Test',
            'is_active' => true,
        ];

        /** @var Client $local */
        $local = (new ApiEntityMaker())->createEntityFromApiPayload(Client::class, $payload);

        $this->assertNotNull($local);
        $this->assertSame($payload['id'], $local->getId());
        $this->assertSame($payload['name'], $local->getName());
        $this->assertSame($payload['is_active'], $local->getIsActive());
    }

    public function test_createEntityFromApiPayload__Deep(): void
    {
        $payload = [
            'id' => 1,
            'name' => 'Test',
            'is_active' => true,
            'project' => [
                'id' => 2,
                'name' => 'Project Name',
                'code' => 'PN',
            ],
            'task' => [
                'id' => 3,
                'Name' => 'Task Name'
            ]
        ];

        /** @var ProjectTask $local */
        $local = (new ApiEntityMaker())->createEntityFromApiPayload(ProjectTask::class, $payload);

        dump($local);

        /*
      "project":{
        "id":14808188,
        "name":"Task Force",
        "code":"TF"
      },
      "task":{
        "id":8083369,
        "name":"Research"
      }
         */
    }
}
