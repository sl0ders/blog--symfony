<?php

namespace App\Controller\Admin;

use App\Datatables\NotificationDatatable;
use App\Entity\Notification;
use App\Repository\NotificationRepository;
use DateTime;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/notification")]
class AdminNotificationController extends AbstractController
{

    /**
     * @var DatatableFactory
     */
    private DatatableFactory $datatableFactory;
    /**
     * @var DatatableResponse
     */
    private DatatableResponse $datatableResponse;

    public function __construct(DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
    {
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
    }

    /**
     * @throws Exception
     */
    #[Route("/", name: "admin_notification_index")]
    public function index(Request $request): JsonResponse|Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->datatableFactory->create(NotificationDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $this->datatableResponse->setDatatable($datatable);
            $this->datatableResponse->getDatatableQueryBuilder();
            return $this->datatableResponse->getResponse();
        }
        return $this->render("admin/notification/index.html.twig", [
            "datatable" => $datatable
        ]);
    }

    #[Route("/show/{id}", name: "admin_notification_show")]
    public function show(Notification $notification)
    {
        if (!$notification->getReadAt()) {
            $notification->setReadAt(new DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();
        }
        return $this->render("admin/notification/show.html.twig", [
            "notification" => $notification
        ]);
    }

    #[Route('/readAt', name: "admin_notification_changeReadAt", options: ["explose" => true])]
    public function changeReadAt(NotificationRepository $notificationRepository, Request $request)
    {
        $notificationId = $request->query->get("id");
        if ($notificationId) {
            $notification = $notificationRepository->find($notificationId);
            if ($notification instanceof Notification) {
                $notification->setReadAt(new DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->persist($notification);
                $em->flush();
                $notify[$notification->getId()] = $notification->getid();
                try {
                    $response = new Response(json_encode($notify, JSON_THROW_ON_ERROR));
                } catch (JsonException $e) {
                    return $e;
                }
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
    }
}
