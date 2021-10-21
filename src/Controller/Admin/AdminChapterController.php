<?php

namespace App\Controller\Admin;

use App\Datatables\ChapterDatatable;
use App\Entity\Chapter;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route("/admin/chapter")]
class AdminChapterController extends AbstractController
{
    private DatatableFactory $datatableFactory;
    private DatatableResponse $datatableResponse;

    public function __construct(DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
    {
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
    }

    /**
     * @throws Exception
     */
    #[Route("/", name: "admin_chapter_index")]
    public function index(Request $request): DatatableResponse|Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->datatableFactory->create(ChapterDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $this->datatableResponse->setDatatable($datatable);
            $this->datatableResponse->getDatatableQueryBuilder();

            return $this->datatableResponse->getResponse();
        }
        return $this->render("admin/chapter/index.html.twig", [
            'datatable' => $datatable,
        ]);
    }

    #[Route("/new", name: "admin_chapter_new")]
    public function new(Request $request, ChapterRepository $chapterRepository)
    {
        $chapter = new Chapter();
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);
        $lastChapter = $chapterRepository->findOneBy([], ["number" => "DESC"]);
        if ($form->isSubmitted() && $form->isValid()) {
            $chapter->setEnabled(true);
            $chapter->setCreatedAt(new \DateTime());
            if ($lastChapter instanceof Chapter) {
                $chapter->setNumber($lastChapter->getNumber() + 1);
            } else {
                $chapter->setNumber(1);
            }


            $em = $this->getDoctrine()->getManager();
            $em->persist($chapter);
            $em->flush();
            $this->addFlash("success", "chapter.new.successfully");
            return $this->redirectToRoute("admin_chapter_index");
        }
        return $this->render("admin/chapter/new.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/enabled/{id}", name: "admin_chapter_enabled")]
    public function enabled(Chapter $chapter, TranslatorInterface $translator)
    {
        if ($chapter->getEnabled() === true) {
            $chapter->setEnabled(false);
            $message = $translator->trans("post.disabled.successFully", [], "BlogTrans");
        } else {
            $chapter->setEnabled(true);
            $message = $translator->trans("post.enabled.successFully", [], "BlogTrans");
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($chapter);
        $em->flush();
        $this->addFlash("success", $message);
        return $this->redirectToRoute("admin_chapter_index");
    }
}
