<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact')]
final class ContactController extends AbstractController
{
    #[Route(name: 'app_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('contact/index.html.twig', [
            'contacts' => $contactRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_contact_new', methods: ['POST'])]
    public function new(EntityManagerInterface $entityManager, UserRepository $userRepository, int $id, ContactRepository $contactRepository): Response
    {
        $receiver = $userRepository->find($id);
        $existingContact = $contactRepository->findBy(['sender' => $this->getUser(), 'receiver' => $receiver]);
        if (!$existingContact) {
            $contact = new Contact();
            $contact->setSender($this->getUser());
            $contact->setReceiver($receiver);
            $contact->setStatus('pending');

            $entityManager->persist($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/invitation', name: 'app_contact_invitation', methods: ['GET'])]
    public function pending(ContactRepository $contactRepository): Response
    {
        $user = $this->getUser();
        return $this->render('contact/invitation.html.twig', [
            'contacts' => $contactRepository->findBy(['receiver' => $user, 'status' => 'pending']),
        ]);
    }

    #[Route('/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contact->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/invitation/{id}/accept', name: 'app_contact_invitation_accept', methods: ['POST'])]
    public function accept(EntityManagerInterface $entityManager, Contact $contact): Response
    {
        $contact->setStatus('accepted');
        $entityManager->persist($contact);
        $entityManager->flush();

        return $this->redirectToRoute('app_contact_index');
    }

    #[Route('/invitation/{id}/refuse', name: 'app_contact_invitation_refuse', methods: ['POST'])]
    public function refuse(EntityManagerInterface $entityManager, Contact $contact): Response
    {
        $contact->setStatus('refused');
        $entityManager->persist($contact);
        $entityManager->flush();

        return $this->redirectToRoute('app_contact_index');
    }
}
