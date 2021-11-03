<?php

namespace App\Services;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\Rating;
use App\Entity\User;
use App\Repository\NotificationRepository;

class NotificationService
{
    private NotificationRepository $notificationRepository;

    /**
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function notify($content, $entity = []): Notification
    {
        $lastNotification = $this->notificationRepository->findOneBy([], ["created_at" => "DESC"]);
        $notification = new Notification();
        $notification->setContent($content)
            ->setCreatedAt(new \DateTime())
            ->setEnabled(true);
        if ($lastNotification instanceof Notification) {
            $notification->setNumber($lastNotification->getNumber() + 1);
        } else {
            $notification->setNumber(1);
        }
        if ($entity) {
            if ($entity instanceof User) {
                $notification->setLink("admin_user_show");
            } elseif ($entity instanceof Post) {
                $notification->setLink("post_show");
            } elseif ($entity instanceof Comment) {
                $notification->setLink("admin_comment_show");
            } elseif ($entity instanceof Rating) {
                $notification->setLink("admin_rate_show");
            } else {
                $notification->setLink("chapter_show");
            }
            $notification->setLinkId($entity->getId());
            $notification->setEnabled(false);
        }
        return $notification;
    }
}
