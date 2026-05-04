<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class UserStatusService
{
    public const STATUT_ACTIF = 'actif';
    public const STATUT_SUSPENDU = 'suspendu';
    public const STATUT_BANNI = 'banni';

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function refresh(User $user): void
    {
        if ($user->getStatus() !== self::STATUT_SUSPENDU) {
            return;
        }

        $suspendedUntil = $user->getSuspendedUntil();

        if ($suspendedUntil === null || $suspendedUntil > new \DateTimeImmutable()) {
            return;
        }

        $user->setStatus(self::STATUT_ACTIF);
        $user->setSuspendedUntil(null);
        $this->entityManager->flush();
    }

    public function getBlockingMessage(User $user): ?string
    {
        $this->refresh($user);

        return match ($user->getStatus()) {
            self::STATUT_BANNI => 'Votre compte a ete banni.',
            self::STATUT_SUSPENDU => $this->buildSuspendedMessage($user),
            default => null,
        };
    }

    private function buildSuspendedMessage(User $user): string
    {
        $suspendedUntil = $user->getSuspendedUntil();

        if ($suspendedUntil === null) {
            return 'Votre compte est suspendu.';
        }

        return sprintf(
            'Votre compte est suspendu jusqu\'au %s.',
            $suspendedUntil->format('d/m/Y H:i')
        );
    }
}
