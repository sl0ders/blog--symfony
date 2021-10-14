<?php

namespace App\Controller;

use App\Entity\Chapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/chapter")]
class ChapterController extends AbstractController
{
    #[Route('/{id}', name: 'chapter_show')]
    public function show(Chapter $chapter): Response
    {
        return $this->render('chapter/show.html.twig', [
            'chapter' => $chapter,
        ]);
    }
}
