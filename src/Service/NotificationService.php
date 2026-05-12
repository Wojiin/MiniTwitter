<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\Discussion;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\Reply;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    ) {
    }

    public function notifyDiscussionMessage(Message $message): void
    {
        $discussion = $message->getDiscussion();
        $author = $message->getSend();

        if (!$discussion instanceof Discussion || !$author instanceof User) {
            return;
        }

        foreach ($discussion->getDiscuss() as $participant) {
            if ($participant->getId() === $author->getId()) {
                continue;
            }

            $this->createNotification(
                recipient: $participant,
                type: 'new_discussion_message',
                content: sprintf(
                    '%s a envoye un nouveau message dans "%s".',
                    $author->getUserName(),
                    $discussion->getTitle()
                ),
                message: $message,
                discussion: $discussion,
            );
        }

        $this->entityManager->flush();
    }

    public function notifyNewPost(Post $post): void
    {
        $author = $post->getCreator();

        if (!$author instanceof User) {
            return;
        }

        foreach ($this->userRepository->findAll() as $recipient) {
            if ($recipient->getId() === $author->getId()) {
                continue;
            }

            $this->createNotification(
                recipient: $recipient,
                type: 'new_post',
                content: sprintf(
                    '%s a publie un nouveau post : %s',
                    $author->getUserName(),
                    $post->getTitle()
                ),
                post: $post,
            );
        }

        $this->entityManager->flush();
    }

    public function notifyReplyOnUserPost(Reply $reply): void
    {
        $post = $reply->getPost();
        $postAuthor = $post?->getCreator();
        $replyAuthor = $reply->getCreator();

        if (!$post instanceof Post || !$postAuthor instanceof User || !$replyAuthor instanceof User) {
            return;
        }

        if ($postAuthor->getId() === $replyAuthor->getId()) {
            return;
        }

        $this->createNotification(
            recipient: $postAuthor,
            type: 'reply_on_user_post',
            content: sprintf(
                '%s a repondu a votre post "%s".',
                $replyAuthor->getUserName(),
                $post->getTitle()
            ),
            post: $post,
            reply: $reply,
        );

        $this->entityManager->flush();
    }

    public function notifyUserAddedToDiscussion(Discussion $discussion, User $addedUser, ?User $actor = null): void
    {
        $content = $actor instanceof User
            ? sprintf('%s vous a ajoute a la discussion "%s".', $actor->getUserName(), $discussion->getTitle())
            : sprintf('Vous avez ete ajoute a la discussion "%s".', $discussion->getTitle());

        $this->createNotification(
            recipient: $addedUser,
            type: 'added_to_discussion',
            content: $content,
            discussion: $discussion,
        );

        $this->entityManager->flush();
    }

    public function notifyContactRequest(Contact $contact): void
    {
        $sender = $contact->getSender();
        $receiver = $contact->getReceiver();

        if (!$sender instanceof User || !$receiver instanceof User) {
            return;
        }

        $this->createNotification(
            recipient: $receiver,
            type: 'contact_request',
            content: sprintf(
                '%s vous a envoye une demande d\'ajout.',
                $sender->getUserName()
            ),
        );

        $this->entityManager->flush();
    }

    private function createNotification(
        User $recipient,
        string $type,
        string $content,
        ?Message $message = null,
        ?Post $post = null,
        ?Reply $reply = null,
        ?Discussion $discussion = null,
    ): void {
        $notification = new Notification();
        $notification
            ->setType($type)
            ->setContent($content)
            ->setIsRead(false)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setReceive($recipient)
            ->setMessageNotif($message)
            ->setPostNotif($post)
            ->setReplyNotif($reply)
            ->setDiscussionNotif($discussion);

        $recipient->setCountNotification(($recipient->getCountNotification() ?? 0) + 1);

        $this->entityManager->persist($notification);
    }
}
