<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\UserStatusService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UserStatusSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UrlGeneratorInterface $urlGenerator,
        private UserStatusService $userStatusService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if (\in_array($route, ['app_login', 'app_register', 'app_logout'], true)) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return;
        }

        $message = $this->userStatusService->getBlockingMessage($user);

        if ($message !== null) {
            $this->forceLogoutAndRedirect($event, $message);
        }
    }

    private function forceLogoutAndRedirect(RequestEvent $event, string $message): void
    {
        $request = $event->getRequest();

        $this->tokenStorage->setToken(null);

        if ($request->hasSession()) {
            $session = $request->getSession();
            $session->remove('_security_main');
            $session->migrate(true);
            $session->getFlashBag()->add('error', $message);
        }

        $event->setResponse(
            new RedirectResponse($this->urlGenerator->generate('app_login'))
        );
    }
}
