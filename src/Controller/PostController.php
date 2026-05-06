<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Event\PostCreatedEvent;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\UploadService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }
    #[Route('/timeline', name: 'app_post_timeline', methods: ['GET'])]
    public function timeline(PostRepository $postRepository): Response
    {
        return $this->render('post/timeline.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UploadService $uploadService, EventDispatcherInterface $eventDispatcher): Response
    {
        $now = new \DateTimeImmutable();
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $post->setCreatedAt($now);
        $post->setStatus('actif');
        $post->setCountFlag(0);
        $post->setCountLike(0);
        $post->setCountRepost(0);
        $post->setAuthor($this->getUser()->getUserName());
        $post->setCreator($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $fileName = $uploadService->upload($imageFile, 'uploads/posts');
                $post->setImage($fileName);
            }

            $entityManager->persist($post);
            $entityManager->flush();
            $eventDispatcher->dispatch(new PostCreatedEvent($post));

            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'replies' => $post->getReplies(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager, UploadService $uploadService): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $post->getCreator() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $now = new \DateTimeImmutable();
        $post->setUpdatedAt($now);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $fileName = $uploadService->upload($imageFile, 'uploads/posts');
                $post->setImage($fileName);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $post->getCreator() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/like/{id}', name: 'app_post_addlike', methods: ['GET', 'POST'])]
    public function addLikePost(Post $post, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // dd("if");
            $user->addLike($post);
            $post->setCountLike(+ ($post->getCountLike()) + 1);
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
    }




    #[Route('/remlike/{id}', name: 'app_post_remlike', methods: ['POST'])]
    public function removeLikePost(Post $post,  EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user->removeLike($post);
            $post->setCountLike(+ ($post->getCountLike()) - 1);
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/repost/{id}', name: 'app_post_addrepost', methods: ['POST'])]
    public function addRepo(Post $post, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user->addRepost($post);
            $post->setCountRepost(+ ($post->getCountRepost()) + 1);
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
    }




    #[Route('/remrepo/{id}', name: 'app_post_remrepost', methods: ['POST'])]
    public function removeRepo(Post $post, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user->removeRepost($post);
            $post->setCountRepost(+ ($post->getCountRepost()) - 1);
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/flag/{id}', name: 'app_post_addflag', methods: ['GET', 'POST'])]
    public function addFlagOnPost(Post $post, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // dd("if");
            $user->addFlagPost($post);
            $post->setCountFlag(+ ($post->getCountFlag()) + 1);
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/unflag/{id}', name: 'app_post_unflag', methods: ['GET', 'POST'])]
    public function removeFlagOnPost(Post $post, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // dd("if");
            $user->removeFlagPost($post);
            $post->setCountFlag(+ ($post->getCountFlag()) - 1);
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_post_timeline', [], Response::HTTP_SEE_OTHER);
    }



    
}
