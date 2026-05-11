<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/notification')]
final class NotificationController extends AbstractController
{
    #[Route(name: 'app_notification_index', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('notification/index.html.twig', [
            'notifications' => $notificationRepository->findForRecipient($user),
        ]);
    }

    #[Route('/{id}/open', name: 'app_notification_open', methods: ['GET'])]
    public function open(Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if ($notification->getReceive()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $user->setCountNotification(max(0, ($user->getCountNotification() ?? 0) - 1));
        $entityManager->remove($notification);
        $entityManager->flush();

        if ($notification->getDiscussionNotif() !== null) {
            return $this->redirectToRoute('app_discussion_show', [
                'id' => $notification->getDiscussionNotif()->getId(),
            ]);
        }

        if ($notification->getType() === 'contact_request') {
            return $this->redirectToRoute('app_contact_invitation');
        }

        if ($notification->getReplyNotif() !== null) {
            return $this->redirectToRoute('app_reply_show', [
                'id' => $notification->getReplyNotif()->getId(),
            ]);
        }

        if ($notification->getPostNotif() !== null) {
            return $this->redirectToRoute('app_post_show', [
                'id' => $notification->getPostNotif()->getId(),
            ]);
        }

        if ($notification->getMessageNotif()?->getDiscussion() !== null) {
            return $this->redirectToRoute('app_discussion_show', [
                'id' => $notification->getMessageNotif()->getDiscussion()->getId(),
            ]);
        }

        return $this->redirectToRoute('app_notification_index');
    }

    #[Route('/{id}/seen', name: 'app_notification_seen', methods: ['POST'])]
    public function seen(Request $request, Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if ($notification->getReceive()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('seen'.$notification->getId(), $request->getPayload()->getString('_token'))) {
            return $this->redirectToRoute('app_notification_index');
        }

        $user->setCountNotification(max(0, ($user->getCountNotification() ?? 0) - 1));
        $entityManager->remove($notification);
        $entityManager->flush();

        return $this->redirectToRoute('app_notification_index');
    }
}
