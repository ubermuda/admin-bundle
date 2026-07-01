<?php

namespace Ubermuda\AdminBundle\Test\Listing;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Ubermuda\AdminBundle\Listing\AdminReturnTo;
use Ubermuda\AdminBundle\Test\RecordingLogger;

final class AdminReturnToTest extends TestCase
{
    public function testAcceptsAdminPrefixedPath(): void
    {
        $returnTo = new AdminReturnTo(new NullLogger());

        self::assertSame('/admin/users', $returnTo->validate('x', '/admin/users'));
    }

    public function testRejectsAndLogsNonAdminPath(): void
    {
        $logger = new RecordingLogger();
        $returnTo = new AdminReturnTo($logger);

        self::assertNull($returnTo->validate('x', '/evil'));
        self::assertTrue($logger->hasRecord('info', 'admin.list.return_to_rejected'));
    }

    public function testRejectsEmptyStringWithoutLogging(): void
    {
        $logger = new RecordingLogger();
        $returnTo = new AdminReturnTo($logger);

        self::assertNull($returnTo->validate('x', ''));
        self::assertSame([], $logger->records);
    }

    public function testRejectsNonStringWithoutLogging(): void
    {
        $logger = new RecordingLogger();
        $returnTo = new AdminReturnTo($logger);

        self::assertNull($returnTo->validate('x', ['/admin']));
        self::assertNull($returnTo->validate('x', null));
        self::assertSame([], $logger->records);
    }
}
