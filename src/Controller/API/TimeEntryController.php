<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Entity\UserTimeEntry;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserTimeEntryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api', name: 'api_time_')]
class TimeEntryController extends AbstractController
{
    #[Route('/favorite', name: 'favorite', methods: 'POST')]
    public function addFavorite(Request $request, Security $security, ProjectRepository $projectRepository, ClientRepository $clientRepository, EntityManagerInterface $entityManager): Response
    {
        $objectId = $request->request->get('object-id');
        $objectType = $request->request->get('object-type');
        $action = $request->request->get('action');

        if (!in_array($objectType, ['project', 'client'], true)) {
            return $this->createErrorResponse('Unknown object type');
        }

        if (!in_array($action, ['add', 'remove'], true)) {
            return $this->createErrorResponse('Unknown action');
        }

        $object = match ($objectType) {
            'project' => $projectRepository->find($objectId),
            'client' => $clientRepository->find($objectId),
        };

        if (!$object) {
            return $this->createErrorResponse('Could not find selected '.$objectType);
        }

        $user = $security->getUser();
        if (!$user instanceof User) {
            return $this->createErrorResponse('Weird unknown user type thing');
        }

        $function = match ($objectType) {
            'project' => match ($action) {
                'add' => $user->addFavoriteProject(...),
                'remove' => $user->removeFavoriteProject(...)
            },
            'client' => match ($action) {
                'add' => $user->addFavoriteClient(...),
                'remove' => $user->removeFavoriteClient(...)
            }
        };

        $function($objectId);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/add/comment', name: 'add_comment', methods: 'POST')]
    public function comment(Request $request, Security $security, ProjectRepository $projectRepository, UserTimeEntryRepository $userTimeEntryRepository): Response
    {
        $fieldId = $request->request->get('field');
        $fieldValue = $request->request->get('value');

        $fieldIdParts = explode('|', $fieldId);
        if (2 !== count($fieldIdParts)) {
            return $this->createErrorResponse('Invalid field id encountered');
        }

        [$projectId, $dateString] = $fieldIdParts;
        $project = $projectRepository->find($projectId);
        if (!$project) {
            return $this->createErrorResponse('Could not find project');
        }

        try {
            $entryDate = new DateTimeImmutable($dateString);
        } catch (\Exception) {
            return $this->createErrorResponse('Could not parse date');
        }

        $user = $security->getUser();
        if (!$user instanceof User) {
            return $this->createErrorResponse('Weird unknown user type thing');
        }

        $timeEntry = $userTimeEntryRepository->findOneBy(['user' => $user, 'project' => $project, 'entryDateInt' => $entryDate->format('Ymd')]);
        if ($timeEntry && (int)$fieldValue === 0 && !$fieldValue) {
            $userTimeEntryRepository->remove($timeEntry, true);
        } else {
            if (!$timeEntry) {
                $timeEntry = new UserTimeEntry($user, $project, $entryDate);
                $timeEntry->setHours(0);
            }
            $timeEntry->setComment($fieldValue);
            $userTimeEntryRepository->save($timeEntry, true);
        }

        return new JsonResponse(['success' => true]);
    }

    #[Route('/add', name: 'add', methods: 'POST')]
    public function add(Request $request, Security $security, ProjectRepository $projectRepository, UserTimeEntryRepository $userTimeEntryRepository): Response
    {
        $fieldId = $request->request->get('field');
        $fieldValue = $request->request->get('value');

        $fieldIdParts = explode('|', $fieldId);
        if (2 !== count($fieldIdParts)) {
            return $this->createErrorResponse('Invalid field id encountered');
        }

        [$projectId, $dateString] = $fieldIdParts;
        $project = $projectRepository->find($projectId);
        if (!$project) {
            return $this->createErrorResponse('Could not find project');
        }

        try {
            $entryDate = new DateTimeImmutable($dateString);
        } catch (\Exception) {
            return $this->createErrorResponse('Could not parse date');
        }

        $user = $security->getUser();
        if (!$user instanceof User) {
            return $this->createErrorResponse('Weird unknown user type thing');
        }

        $timeEntry = $userTimeEntryRepository->findOneBy(['user' => $user, 'project' => $project, 'entryDateInt' => $entryDate->format('Ymd')]);
        if ($timeEntry && (int)$fieldValue === 0 && !$timeEntry->getComment()) {
            $userTimeEntryRepository->remove($timeEntry, true);
        } else {
            if (!$timeEntry) {
                $timeEntry = new UserTimeEntry($user, $project, $entryDate);
            }
            $timeEntry->setHours((float)$fieldValue);
            $userTimeEntryRepository->save($timeEntry, true);
        }

        return new JsonResponse(['success' => true]);
    }

    private function createErrorResponse(string $message): JsonResponse
    {
        return new JsonResponse(['error' => $message], 400);
    }
}