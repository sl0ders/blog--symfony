<?php

namespace App\Controller\Admin;

use App\Datatables\PostDatatable;
use App\Entity\Post;
use App\Entity\Rating;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Services\NotificationService;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request): JsonResponse|Response
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
            if ($user instanceof User) {
                if ($lastPost instanceof Post) {
                    $post->setNumber($lastPost->getNumber() + 1);
                } else {
                    $post->setNumber(1);
                }
                $post->setAuthor($user);
                $entityManager->persist($post);
                $entityManager->flush();

                return $this->redirectToRoute('admin_post_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->render("admin/post/new.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/edit/{id}", name: "admin_post_edit")]
    public function edit(Post $post, Request $request)
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('admin_post_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render("admin/post/edit.html.twig", [
            "form" => $form->createView(),
            "post" => $post
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

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param PostRepository $postRepository
     * @return Exception|JsonException|Response|void
     */
    #[Route("/addRate", name: "admin_post_addRate")]
    public function addRate(Request $request, UserRepository $userRepository, PostRepository $postRepository, NotificationService $notificationService, TranslatorInterface $translator)
    {
        $rate = $request->request->get("rate");
        $userId = $request->request->get("userId");
        $postId = $request->request->get("postId");
        $user = $userRepository->find($userId);
        $post = $postRepository->find($postId);
        if ($user instanceof User && $post instanceof Post) {
            $newRate = new Rating();
            $newRate
                ->setNumber($rate)
                ->setPost($post)
                ->setUser($user);
            $em = $this->getDoctrine()->getManager();
            if (count($post->getRatings()) > 1) {
                $ratesLenght = count($post->getRatings()) + 1;
            } else {
                $ratesLenght = 1;
            }
            $em->persist($newRate);
            $em->flush();
            $notifText = $translator->trans("notification.rate.newRate", ["%postAt%" => date_format($post->getCreatedAt(), "d/m/Y")], "BlogTrans");
           $notification =  $notificationService->notify($notifText);
            $em->persist($notification);
            $em->flush();
            try {
                $response = new Response(json_encode($ratesLenght, JSON_THROW_ON_ERROR));
            } catch (JsonException $e) {
                return $e;
            }
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
}
