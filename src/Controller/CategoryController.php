<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/create', name: 'create_category')]
    public function create(Request $request, PersistenceManagerRegistry $doctrine)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $em = $doctrine->getManager();

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('article');
        }

        return $this->render('category/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/category/single/{category}', name: 'single_category')]
    public function single(Category $category, ArticleRepository $articleRepository, PersistenceManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager(); 

        return $this->render('category/single.html.twig', [
            'category' => $category,
            'categories' => $em->getRepository(Category::class)->findAll(),
            'articles' => $articleRepository->findBy(['category' => $category])
        ]);
    }
}
