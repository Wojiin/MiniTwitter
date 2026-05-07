<?php

namespace App\Controller;

use App\Entity\Reply;
use App\Event\ReplyCreatedEvent;
use App\Form\ReplyType;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use DateTimeImmutable;
use App\Repository\ReplyRepository;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reply')]
final class ReplyController extends AbstractController
{
    #[Route(name: 'app_reply_index', methods: ['GET'])]
    public function index(ReplyRepository $replyRepository): Response
    {
        return $this->render('reply/index.html.twig', [
            'replies' => $replyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reply_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository, UploadService $uploadService, EventDispatcherInterface $eventDispatcher): Response
    {
        $reply = new Reply();
        $form = $this->createForm(ReplyType::class, $reply);
        $form->handleRequest($request);
        if (isset($_GET['id'])) {
            $post = new Post();
            $post = $postRepository->find($_GET['id']);
            $now = new \DateTimeImmutable();
            $reply->setCreatedAt($now);
            $reply->setStatus('actif');
            $reply->setCountFlag(0);
            $reply->setCreator($this->getUser());
            $reply->setPost($post);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $fileName = $uploadService->upload($imageFile, 'uploads/replies');
                $reply->setImage($fileName);
            }

            $entityManager->persist($reply);
            $entityManager->flush();
            $eventDispatcher->dispatch(new ReplyCreatedEvent($reply));
            $this->addFlash('success', 'La réponse a été créée avec succès !');
            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reply/new.html.twig', [
            'reply' => $reply,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reply_show', methods: ['GET'])]
    public function show(Reply $reply): Response
    {
        return $this->render('reply/show.html.twig', [
            'reply' => $reply,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reply_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reply $reply, EntityManagerInterface $entityManager, UploadService $uploadService): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $reply->getCreator() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ReplyType::class, $reply);
        $form->handleRequest($request);
        $now = new \DateTimeImmutable();
        $reply->setUpdatedAt($now);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $fileName = $uploadService->upload($imageFile, 'uploads/replies');
                $reply->setImage($fileName);
            }

            $entityManager->flush();
            $this->addFlash('success', 'La réponse a été modifiée avec succès !');
            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reply/edit.html.twig', [
            'reply' => $reply,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reply_delete', methods: ['POST'])]
    public function delete(Request $request, Reply $reply, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $reply->getCreator() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $reply->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reply);
            $entityManager->flush();
        }
        $this->addFlash('success', 'La réponse a été supprimée avec succès !');
        return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/flagrep/{id}', name: 'app_reply_addflag', methods: ['GET', 'POST'])]
    public function addFlagOnReply(Reply $reply, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // dd("if");
            $user->addFlagReply($reply);
            $reply->setCountFlag(+ ($reply->getCountFlag()) + 1);
            $entityManager->persist($reply);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/unflagrep/{id}', name: 'app_reply_unflag', methods: ['GET', 'POST'])]
    public function removeFlagOnReply(Reply $reply, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // dd("if");
            $user->removeFlagReply($reply);
            $reply->setCountFlag(+ ($reply->getCountFlag()) - 1);
            $entityManager->persist($reply);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
    }
}
