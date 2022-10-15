<?php

namespace App\Controller\Admin;

use App\Entity\ProjectCategory;
use App\Exception\TimeReportingException;
use App\Repository\ClientRepository;
use App\Repository\ProjectCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

#[Route('/admin', name: 'admin_')]
class ProjectController extends AbstractController
{
    private const TOKEN_FOR_PROJECT_CATEGORY_SAVE = 'project-category-post';
    private const TOKEN_FOR_PROJECT_GRID = 'project-grid-post';

    #[Route('/project-category/grid', name: 'project_category_grid', methods: ['GET'])]
    public function projectCategoryGrid(ProjectCategoryRepository $projectCategoryRepository, ClientRepository $clientRepository): Response
    {
        return $this->render(
            'admin/project-category-grid.html.twig',
            [
                'clients' => $clientRepository->findAllActiveClientsAndProjects(),
                'project_categories' => $projectCategoryRepository->findAll(),
                'csrf_token_name' => self::TOKEN_FOR_PROJECT_GRID,
            ]
        );
    }

    #[Route('/project-category/grid', name: 'project_category_grid_save', methods: ['POST'])]
    public function projectCategoryGridSave(ProjectCategoryRepository $projectCategoryRepository, ClientRepository $clientRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid(self::TOKEN_FOR_PROJECT_GRID, $submittedToken)) {
            throw new InvalidCsrfTokenException();
        }

        $all = $request->request->all();
        foreach ($all as $key => $value) {
            if (!str_starts_with($key, 'client::')) {
                continue;
            }

            $clientParts = explode(':', $key);
            if (2 !== count($clientParts)) {
                throw new TimeReportingException('Two parts not found in client radio');
            }

//            $clientId =
        }

        //TODO: return something
    }

    #[Route('/project-category', name: 'project_categories_list', methods: ['GET'])]
    public function projectCategoryList(ProjectCategoryRepository $projectCategoryRepository): Response
    {
        return $this->render(
            'admin/project-categories-list.html.twig',
            [
                'project_categories' => $projectCategoryRepository->findAll(),
                'csrf_token_name' => self::TOKEN_FOR_PROJECT_CATEGORY_SAVE,
            ]
        );
    }

    #[Route('/project-category', name: 'project_categories_save', methods: ['POST'])]
    public function projectCategorySave(Request $request, ProjectCategoryRepository $projectCategoryRepository, EntityManagerInterface $entityManager): Response
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid(self::TOKEN_FOR_PROJECT_CATEGORY_SAVE, $submittedToken)) {
            throw new InvalidCsrfTokenException();
        }

        $newProjectCategoryName = $request->request->get('project-category-name');
        $newSortOrder = $request->request->getInt('sort-order');
        $existingId = $request->request->getInt('project-category-id');

        if ($existingId) {
            $existing = $projectCategoryRepository->find($existingId);
            if (!$existing) {
                $this->addFlash('error', 'Could not find project category to edit');

                return $this->redirectToRoute('admin_project_category_edit', ['projectCategory' => $existingId]);
            }

            $allExistingWithThisName = $projectCategoryRepository->findBy(['name' => $newProjectCategoryName]);
            foreach ($allExistingWithThisName as $existingProjectCategory) {
                if ($existingProjectCategory->getId() !== $existingId) {
                    $this->addFlash('error', 'A category with that name already exists');

                    return $this->redirectToRoute('admin_project_category_edit', ['projectCategory' => $existingId]);
                }
            }

            $existing->setName($newProjectCategoryName);
            $existing->setSortOrder($newSortOrder);
            $entityManager->persist($existing);
            $entityManager->flush();
            $this->addFlash('success', 'Project category updated');

            return $this->redirectToRoute('admin_project_categories_list');
        }

        if (!$newProjectCategoryName) {
            $this->addFlash('error', 'Please enter a name');

            return $this->redirectToRoute('admin_project_categories_list');
        }

        $existing = $projectCategoryRepository->findOneBy(['name' => $newProjectCategoryName]);
        if ($existing) {
            $this->addFlash('error', 'A category with that name already exists');

            return $this->redirectToRoute('admin_project_categories_list');
        }

        $pc = new ProjectCategory($newProjectCategoryName, $newSortOrder);
        $entityManager->persist($pc);
        $entityManager->flush();
        $this->addFlash('success', 'New project category added');

        return $this->redirectToRoute('admin_project_categories_list');
    }

    #[Route('/project-category/{projectCategory}', name: 'project_category_edit')]
    public function projectCategoryEdit(ProjectCategory $projectCategory): Response
    {
        return $this->render(
            'admin/project-category-edit.html.twig',
            [
                'project_category' => $projectCategory,
                'csrf_token_name' => self::TOKEN_FOR_PROJECT_CATEGORY_SAVE,
            ]
        );
    }
}
