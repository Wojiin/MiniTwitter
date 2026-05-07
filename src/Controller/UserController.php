<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileSettingsType;
use App\Form\UserModerationType;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use App\Service\UploadService;
use App\Service\UserStatusService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/profile/edit', name: 'app_user_profile_edit', methods: ['GET', 'POST'])]
public function editProfile(
    Request $request,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $userPasswordHasher,
    UploadService $uploadService
): Response {
    $user = $this->getUser();

    if (!$user instanceof User) {
        throw $this->createAccessDeniedException();
    }

    $form = $this->createForm(ProfileSettingsType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        $currentPassword = $form->get('currentPassword')->getData();
        $plainPassword = $form->get('plainPassword')->getData();

        if (!empty($plainPassword)) {
            if (empty($currentPassword)) {
                $form->get('currentPassword')->addError(
                    new FormError('Veuillez renseigner votre mot de passe actuel pour le modifier.')
                );
            } elseif (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                $form->get('currentPassword')->addError(
                    new FormError('Le mot de passe actuel est incorrect.')
                );
            } else {
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $plainPassword)
                );
            }
        }

        if ($form->isValid()) {
            $imageFile = $form->get('profil_picture')->getData();

            if ($imageFile) {
                $fileName = $uploadService->upload($imageFile, 'uploads/profile_pictures');
                $user->setProfilPicture($fileName);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_profile_edit');
        }
    }

    return $this->render('user/profile_edit.html.twig', [
        'form' => $form,
        'user' => $user,
    ]);
}

    
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, ContactRepository $contactRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'contacts' => $contactRepository->findBy(['sender' => $this->getUser()]),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserModerationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $status = $user->getStatus();
            $suspendedUntil = $user->getSuspendedUntil();

            if ($status === UserStatusService::STATUT_ACTIF || $status === UserStatusService::STATUT_BANNI) {
                $user->setSuspendedUntil(null);
            }

            if ($status === UserStatusService::STATUT_SUSPENDU && $suspendedUntil !== null && $suspendedUntil <= new \DateTimeImmutable()) {
                $form->get('suspended_until')->addError(
                    new FormError('La fin de suspension doit etre dans le futur.')
                );
            }

            if ($user === $this->getUser() && $status !== UserStatusService::STATUT_ACTIF) {
                $form->get('status')->addError(
                    new FormError('Vous ne pouvez pas vous bannir ou vous suspendre vous-meme.')
                );
            }

            if (!$form->isValid()) {
                return $this->render('user/edit.html.twig', [
                    'user' => $user,
                    'form' => $form,
                ]);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/flaguser/{id}', name: 'app_user_addflag', methods: ['GET', 'POST'])]
    public function addFlagOnUser(User $userFlag, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // dd("if");
            $user->addFlagUser($userFlag);
            $userFlag->setCountFlag(+ ($userFlag->getCountFlag()) + 1);
            $entityManager->persist($userFlag);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_show', ['id' => $userFlag->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_user_show', ['id' => $userFlag->getId()], Response::HTTP_SEE_OTHER);
    }

     #[Route('/unflaguser/{id}', name: 'app_user_unflag', methods: ['GET', 'POST'])]
    public function removeFlagOnUser(User $userFlag, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // dd("if");
            $user->removeFlagUser($userFlag);
            $userFlag->setCountFlag(+ ($userFlag->getCountFlag()) - 1);
            $entityManager->persist($userFlag);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_show', ['id' => $userFlag->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_user_show', ['id' => $userFlag->getId()], Response::HTTP_SEE_OTHER);
    }
}
