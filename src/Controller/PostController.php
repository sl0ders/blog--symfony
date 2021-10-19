<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/{id}', name: 'post_show')]
    public function show(Post $post, Request $request, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy(["post" => $post], ["created_at" => "DESC"]);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment
                ->setAuthor($this->getUser())
                ->setCreatedAt(new \DateTime())
                ->setPost($post)
                ->setEnabled(true);
            $em->persist($comment);
            $this->addFlash("success", "comment.sent.success");
            return $this->redirectToRoute('post_show', ["id" => $post->getId()]);
        }
        return $this->render('post/show.html.twig', [
            'post' => $post,
            "comments" => $comments,
            "form" => $form->createView()
        ]);
    }
}
