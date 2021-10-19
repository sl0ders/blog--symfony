<?php

namespace App\Controller\Admin;

use App\Datatables\CommentDatatable;
use App\Datatables\PostDatatable;
use App\Entity\Comment;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Sodium\add;

#[Route("/admin/comment")]
class AdminCommentController extends AbstractController
{
    private DatatableFactory $datatableFactory;
    private DatatableResponse $datatableResponse;

    public function __construct(DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
    {
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
    }

    #[Route("/show/{id}", name: "admin_comment_show")]
    public function show(Comment $comment): Response
    {
        return $this->render("admin/comment/show.html.twig", [
            "comment" => $comment
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route("/", name: "admin_comment_index")]
    public function index(Request $request): JsonResponse|Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->datatableFactory->create(CommentDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $this->datatableResponse->setDatatable($datatable);
            $this->datatableResponse->getDatatableQueryBuilder();

            return $this->datatableResponse->getResponse();
        }
        return $this->render("admin/post/index.html.twig", [
            "datatable" => $datatable
        ]);
    }

    #[Route("/enabled/{id}", name: "admin_comment_enabled")]
    public function enabled(Comment $comment, TranslatorInterface $translator): RedirectResponse
    {
        try {
            if ($comment->getEnabled() === true) {
                $comment->setEnabled(false);
                $message = $translator->trans("comment.isDisabled", [], "BlogTrans");
            } else {
                $comment->setEnabled(true);
                $message = $translator->trans("comment.isEnabled", [], "BlogTrans");
            }
            $this->addFlash("success", $message);
            return $this->redirectToRoute("admin_comment_index");
        } catch (Exception $exception) {
            return dump($exception);
        }
    }
}
