<?php

namespace App\Controller;

use App\DTO\ClientRollup\RollupReportByClient;
use App\DTO\GenericRollup\RollupReportInterface;
use App\DTO\UserRollup\RollupReportByUser;
use App\Entity\UserTimeEntry;
use App\Repository\UserTimeEntryRepository;
use App\Service\DateUtility;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/app/report', name: 'report_')]
class ReportController extends AbstractController
{
    public function __construct(
        private readonly UserTimeEntryRepository $userTimeEntryRepository,
        private readonly DateUtility $dateUtility
    ) {
    }

    private function getReport(RollupReportInterface $report, DateTimeInterface $monday): void
    {
        /** @var UserTimeEntry[] $entries */
        $entries = $this->userTimeEntryRepository->rollupReport((int)$monday->format('Ymd'));
        $report->setItems($entries);
    }

    #[Route('/user/{dateStart}', name: 'user')]
    public function reportByUser(?int $dateStart = null): Response
    {
        $firstDayOfWeek = $this->dateUtility->getCurrentDate($dateStart);

        $report = new RollupReportByUser();
        $this->getReport($report, $firstDayOfWeek);

        return $this->render(
            'report/by-user.html.twig',
            [
                'thisWeek' => $firstDayOfWeek->format('m/d/Y'),
                'previousWeek' => $firstDayOfWeek->modify('-7 days')->format('Ymd'),
                'nextWeek' => $firstDayOfWeek->modify('+7 days')->format('Ymd'),
                'report' => $report,
            ]
        );
    }

    #[Route('/client/{dateStart}', name: 'client')]
    public function reportByClient(?int $dateStart = null): Response
    {
        $firstDayOfWeek = $this->dateUtility->getCurrentDate($dateStart);

        $report = new RollupReportByClient();
        $this->getReport($report, $firstDayOfWeek);

        return $this->render(
            'report/by-client.html.twig',
            [
                'thisWeek' => $firstDayOfWeek->format('m/d/Y'),
                'previousWeek' => $firstDayOfWeek->modify('-7 days')->format('Ymd'),
                'nextWeek' => $firstDayOfWeek->modify('+7 days')->format('Ymd'),
                'report' => $report,
            ]
        );
    }
}