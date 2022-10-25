<?php

namespace App\Controller;

use App\DTO\DayOfWeek;
use App\DTO\SimpleTimeEntry;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserTimeEntryRepository;
use App\Service\DateUtility;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/app/time', name: 'time')]
class TimeController extends AbstractController
{
    public function __construct(
        private readonly DateUtility $dateUtility
    ) {
    }

    #[Route('/{dateStart}', name: '')]
    public function index(ClientRepository $clientRepository, UserTimeEntryRepository $userTimeEntryRepository, Security $security, ?int $dateStart = null): Response
    {
        $firstDayOfWeek = $this->dateUtility->getCurrentDate($dateStart);

        /** @var User $user */
        $user = $security->getUser();

        $userTimeEntriesEntities = $userTimeEntryRepository->findAllByUserAndDateRange($user, $firstDayOfWeek);

        $userTimeEntriesArray = [];
        foreach ($userTimeEntriesEntities as $userTimeEntry) {
            $userTimeEntriesArray[$userTimeEntry->getProject()->getId()][$userTimeEntry->getEntryDate()->format('Y-m-d')] = new SimpleTimeEntry($userTimeEntry->getHours(), $userTimeEntry->getComment());
        }

        return $this->render(
            'time/index.html.twig',
            [
                'label' => sprintf('%1$s - %2$s', $firstDayOfWeek->format('M j'), $firstDayOfWeek->modify('+5 days')->format('M j')),
                'clients' => $clientRepository->findAllActiveClientsAndProjects(),
                'controller_name' => 'TimeController',
                'apiEndpointComment' => $this->generateUrl('api_time_add_comment'),
                'apiEndpointTimeEntry' => $this->generateUrl('api_time_add'),
                'apiEndpointFavorites' => $this->generateUrl('api_time_favorite'),
                'weekday' => new DayOfWeek('Monday', $firstDayOfWeek->format('m/d'), $firstDayOfWeek->format('Y-m-d')),
                'userTimeEntries' => $userTimeEntriesArray,
                'previousWeek' => $firstDayOfWeek->modify('-7 days')->format('Ymd'),
                'nextWeek' => $firstDayOfWeek->modify('+7 days')->format('Ymd'),
                'favoriteProjects' => $user->getFavoriteProjects(),
                'favoriteClients' => $user->getFavoriteClients(),
            ]
        );
    }
}
