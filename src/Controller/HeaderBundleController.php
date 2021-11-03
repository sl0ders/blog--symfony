<?php


namespace App\Controller;

use App\Repository\NotificationRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * this class manage all system of header
 * Class HeaderBundleController
 * @package App\Controller
 */
class HeaderBundleController extends AbstractController
{
    /**
     * this function Manage the notification system of header
     * @param NotificationRepository $notificationRepository
     * @return Response
     * @throws Exception
     */
    public function getNotification(NotificationRepository $notificationRepository): Response
    {
        $notificationsNotRead = [];
        $notifications = $notificationRepository->findBy([], ["created_at" => "DESC"], 10);
        foreach ($notifications as $notification) {
            if (!$notification->getReadAt()) {
                $notificationsNotRead[] = $notification;
            }
        }
        return $this->render("Layout/_header.html.twig", [
            "notifications" => $notifications,
            "notificationsNotRead" => $notificationsNotRead
        ]);
    }
}
