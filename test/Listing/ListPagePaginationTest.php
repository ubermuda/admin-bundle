<?php

namespace Ubermuda\AdminBundle\Test\Listing;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Ubermuda\AdminBundle\Listing\ListPagePagination;
use Ubermuda\AdminBundle\Test\RecordingLogger;

final class ListPagePaginationTest extends TestCase
{
    public function testBuildPageListElidesAroundTheCurrentPage(): void
    {
        $pagination = new ListPagePagination(new NullLogger());

        self::assertSame([1, null, 3, 4, 5, 6, 7, null, 20], $pagination->buildPageList(5, 20));
    }

    public function testBuildPageListWithoutEllipsisWhenAllPagesFit(): void
    {
        $pagination = new ListPagePagination(new NullLogger());

        self::assertSame([1, 2, 3], $pagination->buildPageList(2, 3));
    }

    public function testBuildPageListSinglePage(): void
    {
        $pagination = new ListPagePagination(new NullLogger());

        self::assertSame([1], $pagination->buildPageList(1, 1));
    }

    public function testClampPageReturnsLastPageWhenOutOfRange(): void
    {
        $logger = new RecordingLogger();
        $pagination = new ListPagePagination($logger);

        // 40 items / 20 per page = 2 total pages; requested page 3 clamps to 2.
        self::assertSame(2, $pagination->clampPage('e', 3, 40, 20, []));
        self::assertTrue($logger->hasRecord('info', 'admin.list.page_clamped'));
    }

    public function testClampPageReturnsNullWhenInRange(): void
    {
        $logger = new RecordingLogger();
        $pagination = new ListPagePagination($logger);

        self::assertNull($pagination->clampPage('e', 1, 40, 20, []));
        self::assertSame([], $logger->records);
    }

    public function testClampPageReturnsNullWhenTotalIsZero(): void
    {
        $logger = new RecordingLogger();
        $pagination = new ListPagePagination($logger);

        self::assertNull($pagination->clampPage('e', 3, 0, 20, []));
        self::assertSame([], $logger->records);
    }
}
