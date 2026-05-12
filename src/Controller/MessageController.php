<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/message')]
final class MessageController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_message_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Message $message,
        EntityManagerInterface $entityManager,
        UploadService $uploadService
    ): Response {
        if ($message->getSend() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('picture')->getData();
            $now = new \DateTimeImmutable();

            $message->setContent(trim((string) $message->getContent()));
            $message->setUpdatedAt($now);

            if ($imageFile) {
                $fileName = $uploadService->upload($imageFile, 'uploads/messages');
                $message->setPicture($fileName);
            }

            if ($message->getDiscussion()) {
                $message->getDiscussion()->setUpdatedAt($now);
            }

            $entityManager->flush();

            if ($message->getDiscussion()) {
                return $this->redirectToRoute('app_discussion_show', [
                    'id' => $message->getDiscussion()->getId(),
                ], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_discussion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }
}
