<?php

namespace App\Controller\Admin;

use App\Repository\ProjectCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProjectCategoryRepository $projectCategoryRepository): Response
    {
        return $this->render(
            'admin/index.html.twig',
            [
                'projectCategories' => $projectCategoryRepository->findBy([], ['sortOrder' => 'ASC', 'name' => 'ASC']),
            ]
        );
    }
}