<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }



    #[Route("/enabled/{id}", name: "admin_comment_enabled")]
    public function enabled(Comment $comment, TranslatorInterface $translator): RedirectResponse
    {
        if ($comment->getEnabled() === true) {
            $comment->setEnabled(false);
            $message = $translator->trans("post.disabled.successFully", [], "BlogTrans");
        } else {
            $comment->setEnabled(true);
            $message = $translator->trans("post.enabled.successFully", [], "BlogTrans");
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();
        $this->addFlash("success", $message);
        return $this->redirectToRoute("admin_comment_index");
    }
}
