<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'article')]
    public function index(PersistenceManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $articles = $em->getRepository(Article::class)->findAll();
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    #[Route('/main', name: 'main')]
    public function main(PersistenceManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $articles = $em->getRepository(Article::class)->findAll();
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('article/main.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    #[Route('/article/single/{article}', name: 'single_article')]
    public function single(Article $article, PersistenceManagerRegistry $doctrine)
    {
//        $em = $doctrine->getManager();

        return $this->render('article/single.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/create', name: 'create_article')]
    public function create(Request $request, PersistenceManagerRegistry $doctrine)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setCreatedAt(new \DateTime('now'));

            $em = $doctrine->getManager();

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article');
        }
//        elseif(!$form->isValid()){
//            return
//        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/update/{article}', name: 'update_article')]
    public function update(Request $request, Article $article, PersistenceManagerRegistry $doctrine)
    {
        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $this->generateUrl('update_article', [
                'article' => $article->getId()
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setUpdatedAt(new \DateTime('now'));

            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('article');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/delete/{article}', name: 'article_delete')]
    public function delete(Article $article, PersistenceManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('article');
    }
}
