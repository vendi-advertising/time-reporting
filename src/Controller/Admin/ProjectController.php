<?php

namespace App\Controller\Admin;

use App\Entity\ProjectCategory;
use App\Exception\TimeReportingException;
use App\Repository\ClientRepository;
use App\Repository\ProjectCategoryRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

#[Route('/admin/project-category', name: 'admin_project_category_')]
class ProjectController extends AbstractController
{
    private const TOKEN_FOR_PROJECT_CATEGORY_SAVE = 'project-category-post';
    private const TOKEN_FOR_PROJECT_GRID = 'project-grid-post';

    public function __construct(
        private readonly ProjectCategoryRepository $projectCategoryRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
        private readonly ProjectRepository $projectRepository,
        private readonly ClientRepository $clientRepository,
    ) {
    }

    private function isProjectCategoryNameAlreadyInUse(string $name, ?ProjectCategory $existingProjectCategory = null): bool
    {
        $items = $this->projectCategoryRepository->findBy(['name' => $name]);
        foreach ($items as $item) {
            if ($existingProjectCategory && ($item->getId() !== $existingProjectCategory->getId())) {
                return true;
            }
        }

        return false;
    }

    private function markAllProjectCategoriesAsNotDefault(): void
    {
        foreach ($this->projectCategoryRepository->findBy(['isDefault' => true]) as $projectCategory) {
            $projectCategory->setIsDefault(false);
            $this->entityManager->persist($projectCategory);
        }

        $this->entityManager->flush();
    }

    #[Route('/grid', name: 'grid', methods: ['GET'])]
    public function grid(): Response
    {
        $projectCategories = $this->projectCategoryRepository->findAll();
        $defaultProjectCategory = null;
        foreach ($projectCategories as $projectCategory) {
            if ($projectCategory->isDefault()) {
                $defaultProjectCategory = $projectCategory;
                break;
            }
        }

        return $this->render(
            'admin/project-category-grid.html.twig',
            [
                'clients' => $this->clientRepository->findAllActiveClientsAndProjects(),
                'project_categories' => $projectCategories,
                'default_project_category' => $defaultProjectCategory,
                'csrf_token_name' => self::TOKEN_FOR_PROJECT_GRID,
            ]
        );
    }

    #[Route('/grid', name: 'grid_save', methods: ['POST'])]
    public function gridSave(Request $request): Response
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid(self::TOKEN_FOR_PROJECT_GRID, $submittedToken)) {
            throw new InvalidCsrfTokenException();
        }

        $all = $request->request->all();
        foreach ($all as $key => $value) {
            if (!str_starts_with($key, 'project:')) {
                continue;
            }

            $projectParts = explode(':', $key);
            if (2 !== count($projectParts)) {
                throw new TimeReportingException('Two parts not found in project radio');
            }

            [, $projectId] = $projectParts;

            $project = $this->projectRepository->find((int)$projectId);
            if (!$project) {
                throw new TimeReportingException('Could not find project');
            }

            $projectCategory = $this->projectCategoryRepository->find((int)$value);
            if (!$projectCategory) {
                throw new TimeReportingException('Could not find project category');
            }

            $project->setProjectCategory($projectCategory);
            $this->entityManager->persist($project);
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Updated project categories');

        return $this->redirectToRoute('admin_project_category_grid');
    }

    #[Route('/new', name: 'new', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render(
            'admin/project-categories-list.html.twig',
            [
                'form_action' => $this->router->generate('admin_project_category_new_save'),
                'project_categories' => $this->projectCategoryRepository->findAll(),
                'csrf_token_name' => self::TOKEN_FOR_PROJECT_CATEGORY_SAVE,
            ]
        );
    }

    #[Route('/new', name: 'new_save', methods: ['POST'])]
    public function createSave(Request $request): Response
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid(self::TOKEN_FOR_PROJECT_CATEGORY_SAVE, $submittedToken)) {
            throw new InvalidCsrfTokenException();
        }

        $newProjectCategoryName = $request->request->get('project-category-name');
        $sortOrder = $request->request->getInt('sort-order');
        $isDefault = $request->request->getBoolean('is-default', false);

        if (!$newProjectCategoryName) {
            $this->addFlash('error', 'Please enter a name');

            return $this->redirectToRoute('admin_project_category_new');
        }

        if ($this->isProjectCategoryNameAlreadyInUse($newProjectCategoryName)) {
            $this->addFlash('error', 'A category with that name already exists');

            return $this->redirectToRoute('admin_project_category_new');
        }

        if ($isDefault) {
            $this->markAllProjectCategoriesAsNotDefault();
        }

        $pc = new ProjectCategory($newProjectCategoryName, $sortOrder, $isDefault);
        $this->entityManager->persist($pc);
        $this->entityManager->flush();
        $this->addFlash('success', 'New project category added');

        return $this->redirectToRoute('admin_index');
    }

    #[Route('/edit/{projectCategory}', name: 'edit', methods: ['GET'])]
    public function edit(ProjectCategory $projectCategory): Response
    {
        return $this->render(
            'admin/project-category-edit.html.twig',
            [
                'form_action' => $this->router->generate('admin_project_category_edit_save', ['projectCategory' => $projectCategory->getId()]),
                'project_category' => $projectCategory,
                'csrf_token_name' => self::TOKEN_FOR_PROJECT_CATEGORY_SAVE,
            ]
        );
    }

    #[Route('/edit/{projectCategory}', name: 'edit_save', methods: ['POST'])]
    public function editSave(Request $request, ProjectCategory $projectCategory): Response
    {
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid(self::TOKEN_FOR_PROJECT_CATEGORY_SAVE, $submittedToken)) {
            throw new InvalidCsrfTokenException();
        }

        $newProjectCategoryName = $request->request->get('project-category-name');
        $sortOrder = $request->request->getInt('sort-order');
        $isDefault = $request->request->getBoolean('is-default', false);

        if ($this->isProjectCategoryNameAlreadyInUse($newProjectCategoryName, $projectCategory)) {
            $this->addFlash('error', 'A category with that name already exists');

            return $this->redirectToRoute('admin_project_category_edit', ['projectCategory' => $projectCategory->getId()]);
        }

        if ($isDefault) {
            $this->markAllProjectCategoriesAsNotDefault();
        }

        $projectCategory->setName($newProjectCategoryName);
        $projectCategory->setSortOrder($sortOrder);
        $projectCategory->setIsDefault($isDefault);
        $this->entityManager->persist($projectCategory);
        $this->entityManager->flush();
        $this->addFlash('success', 'Project category updated');

        return $this->redirectToRoute('admin_index');
    }

    #[Route('/delete/{projectCategory}', name: 'delete', methods: ['POST'])]
    public function delete(ProjectCategory $projectCategory, Request $request): Response
    {
        $submittedToken = $request->request->get('token');
        $submittedTokenId = $request->request->get('token-id');
        if (!$this->isCsrfTokenValid($submittedTokenId, $submittedToken)) {
            throw new InvalidCsrfTokenException();
        }

        $projectsWithThisCategory = $this->projectRepository->findBy(['projectCategory' => $projectCategory]);
        foreach ($projectsWithThisCategory as $project) {
            $project->removeProjectCategory();
            $this->entityManager->persist($project);
        }

        $this->entityManager->remove($projectCategory);
        $this->entityManager->flush();

        $this->addFlash('success', 'Successfully deleted project category');

        return $this->redirectToRoute('admin_index');
    }
}
