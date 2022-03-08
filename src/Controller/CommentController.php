<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

class CommentController extends AbstractController
{
    #[Route('/comments/create/{article}', name: 'comment_create_form')]
    public function create(Request $request, Article $article, PersistenceManagerRegistry $doctrine)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_create_form', [
                'article' => $article->getId()
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setCreatedAt(new \DateTime('now'));
            $comment->setArticle($article);

            $em = $doctrine->getManager();

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('single_article', ['article' => $article->getId()]);
        }

        return $this->render('comment/form.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route('/comments/update/{article}/{comment}', name: 'comment_update_form')]
    public function update(Request $request, Article $article, Comment $comment, PersistenceManagerRegistry $doctrine)
    {
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_update_form', [
                'article' => $article->getId(),
                'comment' => $comment->getId()
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setUpdatedAt(new \DateTime('now'));

            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('single_article', ['article' => $article->getId()]);
        }

        return $this->render('comment/form.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route('/comments/delete/{article}/{comment}', name: 'comment_delete')]
    public function delete(Article $article, Comment $comment, PersistenceManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('single_article', ['article' => $article->getId()]);
    }
}
