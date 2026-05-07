<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Entity\Message;
use App\Entity\User;
use App\Event\DiscussionMessageCreatedEvent;
use App\Event\UserAddedToDiscussionEvent;
use App\Form\DiscussionParticipantsType;
use App\Form\DiscussionType;
use App\Form\MessageType;
use App\Repository\DiscussionRepository;
use App\Service\UploadService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[Route('/discussion')]
final class DiscussionController extends AbstractController
{
    #[Route(name: 'app_discussion_index', methods: ['GET'])]
    public function index(DiscussionRepository $discussionRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('discussion/index.html.twig', [
            'discussions' => $discussionRepository->findForParticipant($user),
        ]);
    }

    #[Route('/new', name: 'app_discussion_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UploadService $uploadService,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion, [
            'current_user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $firstMessageContent = (string) $form->get('first_message')->getData();
            $firstMessageImage = $form->get('first_message_picture')->getData();
            $firstMessage = null;

            if (!$discussion->getDiscuss()->contains($user)) {
                $discussion->addDiscuss($user);
            }

            $discussion->setCreatedAt($now);

            $title = trim((string) $discussion->getTitle());

            if ($title === '') {
                $participantNames = [];

                foreach ($discussion->getDiscuss() as $participant) {
                    if ($participant->getId() === $user->getId()) {
                        continue;
                    }

                    $participantNames[] = $participant->getUserName();
                }

                $discussion->setTitle('discussion avec : ' . implode(', ', $participantNames));
            }

            $discussion->setUpdatedAt($now);

            $entityManager->persist($discussion);
            if ($firstMessageContent !== '') {
                $firstMessage = new Message();
                $firstMessage->setContent(trim($firstMessageContent));
                $firstMessage->setCreatedAt($now);
                $firstMessage->setSend($user);
                $firstMessage->setDiscussion($discussion);

                if ($firstMessageImage) {
                    $fileName = $uploadService->upload($firstMessageImage, 'uploads/messages');
                    $firstMessage->setPicture($fileName);
                }

                $entityManager->persist($firstMessage);
            }
            $entityManager->flush();

            foreach ($discussion->getDiscuss() as $participant) {
                if ($participant->getId() === $user->getId()) {
                    continue;
                }

                $eventDispatcher->dispatch(new UserAddedToDiscussionEvent($discussion, $participant, $user));
            }

            if ($firstMessage instanceof Message) {
                $eventDispatcher->dispatch(new DiscussionMessageCreatedEvent($firstMessage));
            }
            $this->addFlash('success', 'Votre discussion a été créée avec succès !');
            return $this->redirectToRoute('app_discussion_show', [
                'id' => $discussion->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('discussion/new.html.twig', [
            'discussion' => $discussion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_discussion_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Discussion $discussion,
        DiscussionRepository $discussionRepository,
        EntityManagerInterface $entityManager,
        UploadService $uploadService,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$discussion->getDiscuss()->contains($user)) {
            throw $this->createAccessDeniedException('Vous ne faites pas partie de cette discussion.');
        }

        $message = new Message();
        $messageForm = $this->createForm(MessageType::class, $message);
        $messageForm->handleRequest($request);

        $participantsForm = $this->createForm(DiscussionParticipantsType::class, null, [
            'current_user' => $user,
            'discussion' => $discussion,
        ]);
        $participantsForm->handleRequest($request);

        if ($messageForm->isSubmitted() && $messageForm->isValid()) {
            $now = new \DateTimeImmutable();
            $imageFile = $messageForm->get('picture')->getData();
            $message->setContent(trim((string) $message->getContent()));

            $message->setCreatedAt($now);
            $message->setSend($user);
            $message->setDiscussion($discussion);

            if ($imageFile) {
                $fileName = $uploadService->upload($imageFile, 'uploads/messages');
                $message->setPicture($fileName);
            }

            $discussion->setUpdatedAt($now);

            $entityManager->persist($message);
            $entityManager->flush();
            $eventDispatcher->dispatch(new DiscussionMessageCreatedEvent($message));

            return $this->redirectToRoute('app_discussion_show', [
                'id' => $discussion->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        if ($participantsForm->isSubmitted() && $participantsForm->isValid()) {
            /** @var iterable<User> $participantsToAdd */
            $participantsToAdd = $participantsForm->get('participants')->getData();

            foreach ($participantsToAdd as $participant) {
                $discussion->addDiscuss($participant);
            }

            $discussion->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            foreach ($participantsToAdd as $participant) {
                $eventDispatcher->dispatch(new UserAddedToDiscussionEvent($discussion, $participant, $user));
            }

            return $this->redirectToRoute('app_discussion_show', [
                'id' => $discussion->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        $messages = $discussion->getMessages()->matching(
            Criteria::create()->orderBy(['created_at' => Criteria::ASC])
        );

        return $this->render('discussion/show.html.twig', [
            'discussion' => $discussion,
            'discussions' => $discussionRepository->findForParticipant($user),
            'participants' => $discussion->getDiscuss(),
            'messages' => $messages,
            'message_form' => $messageForm->createView(),
            'participants_form' => $participantsForm->createView(),
        ]);
    }

    #[Route('/{id}/leave', name: 'app_discussion_leave', methods: ['POST'])]
    public function leave(
        Request $request,
        Discussion $discussion,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('leave' . $discussion->getId(), $request->getPayload()->getString('_token'))) {
            return $this->redirectToRoute('app_discussion_show', ['id' => $discussion->getId()], Response::HTTP_SEE_OTHER);
        }

        if (!$discussion->getDiscuss()->contains($user)) {
            throw $this->createAccessDeniedException('Vous ne faites pas partie de cette discussion.');
        }

        $discussion->removeDiscuss($user);

        if ($discussion->getDiscuss()->count() <= 1) {
            $entityManager->remove($discussion);
        } else {
            $discussion->setUpdatedAt(new \DateTimeImmutable());
        }

        $entityManager->flush();
        $this->addFlash('success', 'Vous avez quitté la discussion avec succès !');
        return $this->redirectToRoute('app_discussion_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_discussion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Discussion $discussion, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(DiscussionType::class, $discussion, [
            'current_user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre discussion a été modifiée avec succès !');
            return $this->redirectToRoute('app_discussion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('discussion/edit.html.twig', [
            'discussion' => $discussion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_discussion_delete', methods: ['POST'])]
    public function delete(Request $request, Discussion $discussion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $discussion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($discussion);
            $entityManager->flush();
            $this->addFlash('success', 'Votre discussion a été supprimée avec succès !');
        }

        return $this->redirectToRoute('app_discussion_index', [], Response::HTTP_SEE_OTHER);
    }
}
