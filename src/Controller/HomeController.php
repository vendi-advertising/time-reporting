<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ApiEntityMaker;
use App\Service\HarvestApiFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(HarvestApiFetcher $fetcher, ApiEntityMaker $entityMaker ): Response
    {
        $payloadString = <<<'TAG'
{
  "id":3230547,
  "first_name":"Jim",
  "last_name":"Allen",
  "email":"jimallen@example.com",
  "telephone":"",
  "timezone":"Mountain Time (US & Canada)",
  "has_access_to_all_future_projects":false,
  "is_contractor":false,
  "is_admin":false,
  "is_project_manager":false,
  "can_see_rates":false,
  "can_create_projects":false,
  "can_create_invoices":false,
  "is_active":true,
  "created_at":"2020-05-01T22:34:41Z",
  "updated_at":"2020-05-01T22:34:52Z",
  "weekly_capacity":126000,
  "default_hourly_rate":100.0,
  "cost_rate":50.0,
  "roles":["Developer"],
  "avatar_url":"https://cache.harvestapp.com/assets/profile_images/abraj_albait_towers.png?1498516481"
}
TAG;
//        $entityMaker->createEntityFromApiPayload(
//            User::class,
//            json_decode($payloadString, true, 512, JSON_THROW_ON_ERROR)
//        );
//        $fetcher->loadClients();
//        $fetcher->loadProjects();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
