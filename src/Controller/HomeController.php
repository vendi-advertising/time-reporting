<?php

namespace App\Controller;

use App\Service\HarvestApiFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(HarvestApiFetcher $fetcher): Response
    {
        dump($fetcher->getUserAssignmentsByUserId(309800));

//        dump($clientRepository->findAllActiveClientsAndProjects());

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
