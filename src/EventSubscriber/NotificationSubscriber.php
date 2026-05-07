<?php

namespace App\EventSubscriber;

use App\Event\DiscussionMessageCreatedEvent;
use App\Event\PostCreatedEvent;
use App\Event\ReplyCreatedEvent;
use App\Event\UserAddedToDiscussionEvent;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class NotificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private NotificationService $notificationService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DiscussionMessageCreatedEvent::class => 'onDiscussionMessageCreated',
            PostCreatedEvent::class => 'onPostCreated',
            ReplyCreatedEvent::class => 'onReplyCreated',
            UserAddedToDiscussionEvent::class => 'onUserAddedToDiscussion',
        ];
    }

    public function onDiscussionMessageCreated(DiscussionMessageCreatedEvent $event): void
    {
        $this->notificationService->notifyDiscussionMessage($event->getMessage());
    }

    public function onPostCreated(PostCreatedEvent $event): void
    {
        $this->notificationService->notifyNewPost($event->getPost());
    }

    public function onReplyCreated(ReplyCreatedEvent $event): void
    {
        $this->notificationService->notifyReplyOnUserPost($event->getReply());
    }

    public function onUserAddedToDiscussion(UserAddedToDiscussionEvent $event): void
    {
        $this->notificationService->notifyUserAddedToDiscussion(
            $event->getDiscussion(),
            $event->getAddedUser(),
            $event->getActor(),
        );
    }
}
