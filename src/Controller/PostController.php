<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\RatingRepository;
use App\Services\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\DataCollector\NotificationDataCollector;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/{id}', name: 'post_show')]
    public function show(Post $post, Request $request, CommentRepository $commentRepository, NotificationService $notificationService, TranslatorInterface $translator, RatingRepository $ratingRepository): Response
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            $userRatingExist = $ratingRepository->findOneBy(["user" => $user, "post" => $post]);
        }
        $comments = $commentRepository->findBy(["post" => $post], ["created_at" => "DESC"]);
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $ratePostAverage = $ratingRepository->findRateAverage($post);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($user instanceof User) {
                $comment->setAuthor($user);
            }
            $comment
                ->setCreatedAt(new \DateTime())
                ->setPost($post)
                ->setEnabled(true);
            $em->persist($comment);
            $em->flush();
            $notifMessage = $translator->trans("notification.comment.new", [], "BlogTrans");
            $notification = $notificationService->notify($notifMessage, $comment);
            $em->persist($notification);
            $em->flush();
            $this->addFlash("success", "comment.sent.success");
            return $this->redirectToRoute('post_show', ["id" => $post->getId()]);
        }
        return $this->render('post/show.html.twig', [
            'post' => $post,
            "rateAverage" => $ratePostAverage["rateAverage"],
            "comments" => $comments,
            "form" => $commentForm->createView(),
            "userRatingExist" => $userRatingExist
        ]);
    }
}
