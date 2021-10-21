<?php

namespace App\Controller\Admin;

use App\Datatables\ChapterDatatable;
use App\Datatables\PostDatatable;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
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

#[Route("/admin/post")]
class AdminPostController extends AbstractController
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
    #[Route("/", name: "admin_post_index")]
    public function index(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->datatableFactory->create(PostDatatable::class);
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

    #[Route("/new", name: "admin_post_new")]
    public function new(Request $request, PostRepository $postRepository): RedirectResponse|Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $lastPost = $postRepository->findOneBy([], ["number" => "DESC"]);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            if($lastPost instanceof Post) {
                $post->setNumber($lastPost->getNumber() + 1);
            } else {
                $post->setNumber(1);
            }
            $post->setAuthor($user);
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('admin_post_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render("admin/post/new.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/enabled/{id}", name: "admin_post_enabled")]
    public function enabled(Post $post, TranslatorInterface $translator): RedirectResponse
    {
        if ($post->getEnabled() === true) {
            $post->setEnabled(false);
            $message = $translator->trans("post.disabled.successFully", [], "BlogTrans");
        } else {
            $post->setEnabled(true);
            $message = $translator->trans("post.enabled.successFully", [], "BlogTrans");
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
        $this->addFlash("success", $message);
        return $this->redirectToRoute("admin_post_index");
    }
}
