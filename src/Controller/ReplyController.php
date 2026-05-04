<?php

namespace App\Controller;

use App\Entity\Reply;
use App\Form\ReplyType;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use DateTimeImmutable;
use App\Repository\ReplyRepository;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function new(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository, UploadService $uploadService): Response
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

            return $this->redirectToRoute('app_reply_index', [], Response::HTTP_SEE_OTHER);
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

            return $this->redirectToRoute('app_reply_index', [], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('app_reply_index', [], Response::HTTP_SEE_OTHER);
    }
}
