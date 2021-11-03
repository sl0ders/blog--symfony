<?php

namespace App\Controller;

use App\Repository\ChapterRepository;
use App\Repository\CommentRepository;
use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CommentRepository $commentRepository, ChapterRepository $chapterRepository, PostRepository $postRepository): Response
    {
        $chapters = $chapterRepository->findAll();
        $posts = $postRepository->findBy(["enabled" => true], ["created_at" => "DESC"], 3);
        $comments = $commentRepository->findAll();
        return $this->render('index.html.twig', [
            "chapters" => $chapters,
            "posts" => $posts,
            "comments" => $comments
        ]);
    }
}
