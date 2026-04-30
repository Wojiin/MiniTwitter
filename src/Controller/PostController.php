<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Reply;
use App\Entity\User;
// use App\Entity\User;
use App\Form\PostType;
use App\Form\ReplyType;
use App\Repository\PostRepository;
use App\Repository\ReplyRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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


    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
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
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post, Reply $reply): Response
    {
        $replies = $post->getReplies();
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'reply' => $replies
        ]);
    }
    // #[Route('/newRep', name: 'app_reply_new', methods: ['GET', 'POST'])]
    // public function newReply(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository): Response
    // {
    //     $reply = new Reply();
    //     $form = $this->createForm(ReplyType::class, $reply);
    //     $form->handleRequest($request);
    //     if (isset($_GET['id'])) {
    //         $post = new Post();
    //         $post = $postRepository->find($_GET['id']);
    //         $now = new \DateTimeImmutable();
    //         $reply->setCreatedAt($now);
    //         $reply->setStatus('actif');
    //         $reply->setCountFlag(0);
    //         $reply->setCreator($this->getUser());
    //         $reply->setPost($post);
    //     }

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $entityManager->persist($reply);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_post_show', ['id'=>'reply.post_id'], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('reply/new.html.twig', [
    //         'reply' => $reply,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $now = new \DateTimeImmutable();
        $post->setUpdatedAt($now);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route(name: 'app_post_like', methods: ['GET'])]
    public function addlike(Post $post, User $user, EntityManagerInterface $entityManager): Response
    {
        $user->addLike($post);
        $post->setCountLike(($post->getCountLike()) + 1);
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'reply' => $replies
        ]);
    }
}
