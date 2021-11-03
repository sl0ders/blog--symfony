<?php

namespace App\Controller\Admin;

use App\Datatables\PostDatatable;
use App\Datatables\UserDatatable;
use App\Entity\User;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route("/admin/user")]
class AdminUserController extends AbstractController
{
    private DatatableFactory $datatableFactory;
    private DatatableResponse $datatableResponse;

    public function __construct(DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
    {
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
    }

    /**
     * @throws \Exception
     */
    #[Route("/", name: "admin_user_index")]
    public function index(Request $request): JsonResponse|Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->datatableFactory->create(UserDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $this->datatableResponse->setDatatable($datatable);
            $this->datatableResponse->getDatatableQueryBuilder();
            return $this->datatableResponse->getResponse();
        }
        return $this->render("admin/user/index.html.twig", [
            "datatable" => $datatable
        ]);
    }

    #[Route("/show/{id}", name: "admin_user_show")]
    public function show(User $user): Response
    {
        return $this->render("admin/user/show.html.twig", [
            "user" => $user
        ]);
    }

    #[Route("/enabled/{id}", name: "admin_user_enabled")]
    public function enabled(User $comment, TranslatorInterface $translator): RedirectResponse
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
