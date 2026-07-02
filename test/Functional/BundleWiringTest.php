<?php

namespace Ubermuda\AdminBundle\Test\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BundleWiringTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return AdminTestKernel::class;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // FrameworkBundle::boot() registers an ErrorHandler exception handler that
        // kernel shutdown does not pop; restore it so PHPUnit does not flag the test risky.
        restore_exception_handler();
    }

    public function testConfigParametersAreRegistered(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        self::assertTrue($container->hasParameter('ubermuda_admin.brand_label'));
        self::assertSame('Admin', $container->getParameter('ubermuda_admin.brand_label'));
        self::assertSame('app_dashboard', $container->getParameter('ubermuda_admin.app_route'));
        self::assertSame('app', $container->getParameter('ubermuda_admin.importmap_entry'));
        self::assertNull($container->getParameter('ubermuda_admin.admin_email'));
        self::assertSame('ROLE_ADMIN', $container->getParameter('ubermuda_admin.admin_role'));
    }

    public function testPromotionListenerIsAbsentWithoutDoctrine(): void
    {
        self::bootKernel();

        // The test kernel ships no DoctrineBundle, so the conditional
        // registration in loadExtension must skip the listener.
        self::assertFalse(
            self::getContainer()->has(\Ubermuda\AdminBundle\Security\PromoteAdminUserListener::class),
        );
    }
}
