<?php

namespace App\Controller;

use App\DTO\DayOfWeek;
use App\DTO\Rollup\RollupClient;
use App\DTO\Rollup\RollupReport;
use App\Entity\User;
use App\Entity\UserTimeEntry;
use App\Repository\ClientRepository;
use App\Repository\UserTimeEntryRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TimeController extends AbstractController
{
    #[Route('/app/report/{dateStart}', name: 'report')]
    public function report(UserTimeEntryRepository $userTimeEntryRepository, ClientRepository $clientRepository, ?int $dateStart = null): Response
    {
        if (!$dateStart) {
            $dateStartObj = new DateTimeImmutable;
        } else {
            $dateStartObj = DateTimeImmutable::createFromFormat('Ymd', (string)$dateStart);
        }

//        $entryDate = new \DateTimeImmutable('2022-10-10');
        /** @var UserTimeEntry[] $entries */
        $entries = $userTimeEntryRepository->rollupReport((int)$dateStartObj->format('Ymd'));
        $report = new RollupReport($entries);

        $monday = $dateStartObj->modify('Monday this week');
        $previousWeek = $monday->modify('-7 days');
        $nextWeek = $monday->modify('+7 days');

//        dump($report);

        return $this->render(
            'report/index.html.twig',
            [
                'thisWeek' => $monday->format('m/d/Y'),
                'previousWeek' => $previousWeek->format('Ymd'),
                'nextWeek' => $nextWeek->format('Ymd'),
                'report' => $report,
//                'clients' => $outputClients,
//                'entries' => $userTimeEntryRepository->findAll(),
            ]
        );
    }

    #[Route('/app/time/{dateStart}', name: 'time')]
    public function index(ClientRepository $clientRepository, UserTimeEntryRepository $userTimeEntryRepository, Security $security, ?int $dateStart = null): Response
    {
        if (!$dateStart) {
            $dateStartObj = new DateTimeImmutable;
        } else {
            $dateStartObj = DateTimeImmutable::createFromFormat('Ymd', (string)$dateStart);
        }

        $monday = $dateStartObj->modify('Monday this week');
        $friday = $monday->modify('+4 day');
        $previousWeek = $monday->modify('-7 days');
        $nextWeek = $monday->modify('+7 days');

        /** @var User $user */
        $user = $security->getUser();

        $userTimeEntriesEntities = $userTimeEntryRepository->findAllByUserAndDateRange($user, $monday, $friday);

        $userTimeEntriesArray = [];
        foreach ($userTimeEntriesEntities as $userTimeEntry) {
            $userTimeEntriesArray[$userTimeEntry->getProject()->getId()][$userTimeEntry->getEntryDate()->format('Y-m-d')] = $userTimeEntry->getHours();
        }

        return $this->render(
            'time/index.html.twig',
            [
                'clients' => $clientRepository->findAllActiveClientsAndProjects(),
                'controller_name' => 'TimeController',
                'apiEndpointTimeEntry' => $this->generateUrl('api_time_add'),
                'apiEndpointFavorites' => $this->generateUrl('api_time_favorite'),
                'dates' => [
                    new DayOfWeek('Monday', $monday->format('m/d'), $monday->format('Y-m-d')),
                    new DayOfWeek('Tuesday', $monday->modify('+1 day')->format('m/d'), $monday->modify('+1 day')->format('Y-m-d')),
                    new DayOfWeek('Wednesday', $monday->modify('+2 day')->format('m/d'), $monday->modify('+2 day')->format('Y-m-d')),
                    new DayOfWeek('Thursday', $monday->modify('+3 day')->format('m/d'), $monday->modify('+3 day')->format('Y-m-d')),
                    new DayOfWeek('Friday', $monday->modify('+4 day')->format('m/d'), $monday->modify('+4 day')->format('Y-m-d')),
                ],
                'userTimeEntries' => $userTimeEntriesArray,
                'previousWeek' => $previousWeek->format('Ymd'),
                'nextWeek' => $nextWeek->format('Ymd'),
                'favoriteProjects' => $user->getFavoriteProjects(),
                'favoriteClients' => $user->getFavoriteClients(),
            ]
        );
    }
}
