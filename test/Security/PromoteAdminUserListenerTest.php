<?php

namespace Ubermuda\AdminBundle\Test\Security;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Ubermuda\AdminBundle\Security\PromoteAdminUserListener;
use Ubermuda\AdminBundle\Test\RecordingLogger;

final class PromoteAdminUserListenerTest extends TestCase
{
    private function loginEvent(UserInterface $user): LoginSuccessEvent
    {
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        $event = $this->createMock(LoginSuccessEvent::class);
        $event->method('getAuthenticatedToken')->willReturn($token);
        $event->method('getFirewallName')->willReturn('main');

        return $event;
    }

    public function testPromotesMatchingUserAndRefreshesToken(): void
    {
        $user = new PromotableUser(email: 'admin@example.com', roles: []);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('flush');
        $tokenStorage = new TokenStorage();
        $logger = new RecordingLogger();

        $listener = new PromoteAdminUserListener($entityManager, $tokenStorage, $logger, 'admin@example.com', 'ROLE_ADMIN');
        $listener($this->loginEvent($user));

        self::assertSame(['ROLE_ADMIN'], $user->roles);
        self::assertTrue($logger->hasRecord('info', 'admin.user.promoted'));

        $token = $tokenStorage->getToken();
        self::assertNotNull($token);
        self::assertSame($user, $token->getUser());
        self::assertContains('ROLE_ADMIN', $token->getRoleNames());
    }

    public function testGrantsConfiguredRole(): void
    {
        $user = new PromotableUser(email: 'admin@example.com', roles: ['ROLE_USER']);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('flush');

        $listener = new PromoteAdminUserListener($entityManager, new TokenStorage(), new RecordingLogger(), 'admin@example.com', 'ROLE_SUPER_ADMIN');
        $listener($this->loginEvent($user));

        self::assertSame(['ROLE_USER', 'ROLE_SUPER_ADMIN'], $user->roles);
    }

    public function testDisabledWhenAdminEmailNullOrEmpty(): void
    {
        $user = new PromotableUser(email: 'admin@example.com', roles: []);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::never())->method('flush');

        foreach ([null, ''] as $adminEmail) {
            $listener = new PromoteAdminUserListener($entityManager, new TokenStorage(), new RecordingLogger(), $adminEmail, 'ROLE_ADMIN');
            $listener($this->loginEvent($user));
        }

        self::assertSame([], $user->roles);
    }

    public function testIgnoresNonMatchingEmail(): void
    {
        $user = new PromotableUser(email: 'someone@example.com', roles: []);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::never())->method('flush');

        $listener = new PromoteAdminUserListener($entityManager, new TokenStorage(), new RecordingLogger(), 'admin@example.com', 'ROLE_ADMIN');
        $listener($this->loginEvent($user));

        self::assertSame([], $user->roles);
    }

    public function testIgnoresUserNotImplementingPromotableInterface(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getRoles')->willReturn([]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::never())->method('flush');

        $listener = new PromoteAdminUserListener($entityManager, new TokenStorage(), new RecordingLogger(), 'admin@example.com', 'ROLE_ADMIN');
        $listener($this->loginEvent($user));
    }

    public function testSkipsUserAlreadyHoldingRole(): void
    {
        $user = new PromotableUser(email: 'admin@example.com', roles: ['ROLE_ADMIN']);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::never())->method('flush');
        $tokenStorage = new TokenStorage();

        $listener = new PromoteAdminUserListener($entityManager, $tokenStorage, new RecordingLogger(), 'admin@example.com', 'ROLE_ADMIN');
        $listener($this->loginEvent($user));

        self::assertSame(['ROLE_ADMIN'], $user->roles);
        self::assertNull($tokenStorage->getToken());
    }
}
