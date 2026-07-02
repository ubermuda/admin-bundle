<?php

namespace Ubermuda\AdminBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Grants the configured admin role to the user whose email matches
 * ubermuda_admin.admin_email, on login. Disabled when admin_email is
 * null or empty. Registered only when DoctrineBundle is present.
 */
#[AsEventListener]
final readonly class PromoteAdminUserListener
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TokenStorageInterface $tokenStorage,
        private LoggerInterface $logger,
        private ?string $adminEmail,
        private string $adminRole,
    ) {
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        if (null === $this->adminEmail || '' === $this->adminEmail) {
            return;
        }

        $user = $event->getAuthenticatedToken()->getUser();

        if (!$user instanceof AdminPromotableUser) {
            return;
        }

        if ($this->adminEmail !== $user->email) {
            return;
        }

        if (in_array($this->adminRole, $user->roles, true)) {
            return;
        }

        $user->roles = [...$user->roles, $this->adminRole];
        $this->entityManager->flush();

        $this->logger->info('admin.user.promoted', [
            'email' => $user->email,
            'role' => $this->adminRole,
        ]);

        // The token was created before this listener ran, so its cached role
        // list doesn't include the admin role yet. Replace it with a fresh
        // token carrying the correct roles; ContextListener will persist this
        // to the session, preventing a "user has changed" deauthentication on
        // the very next request.
        $this->tokenStorage->setToken(
            new UsernamePasswordToken($user, $event->getFirewallName(), $user->getRoles()),
        );
    }
}
