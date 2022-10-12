<?php

namespace App\Controller;

use App\DTO\DayOfWeek;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TimeController extends AbstractController
{
    #[Route('/app/time', name: 'time')]
    public function index(ClientRepository $clientRepository, ProjectRepository $projectRepository, Security $security): Response
    {
        $dateTime = new \DateTimeImmutable();
        $monday = $dateTime->modify(('Sunday' === $dateTime->format('l')) ? 'Monday last week' : 'Monday this week');
        $friday = $monday->modify('+4 day');

//        $userProjects = $projectRepository->findBy(
//            [
//                'user' => $security->getUser(),
//            ]
//        );

//        dd($userProjects);

        return $this->render(

            'time/index.html.twig',
            [
                'clients' => $clientRepository->findAllActiveClientsAndProjects(),
                'controller_name' => 'TimeController',
                'apiEndpointTimeEntry' => $this->generateUrl('api_time_add'),
                'dates' => [
                    new DayOfWeek('Monday', $monday->format('m/d'), $monday->format('Y-m-d')),
                    new DayOfWeek('Tuesday', $monday->modify('+1 day')->format('m/d'), $monday->modify('+1 day')->format('Y-m-d')),
                    new DayOfWeek('Wednesday', $monday->modify('+2 day')->format('m/d'), $monday->modify('+2 day')->format('Y-m-d')),
                    new DayOfWeek('Thursday', $monday->modify('+3 day')->format('m/d'), $monday->modify('+3 day')->format('Y-m-d')),
                    new DayOfWeek('Friday', $monday->modify('+4 day')->format('m/d'), $monday->modify('+4 day')->format('Y-m-d')),
                ],
            ]
        );
    }
}
