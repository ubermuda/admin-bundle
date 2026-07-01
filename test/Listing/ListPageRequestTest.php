<?php

namespace Ubermuda\AdminBundle\Test\Listing;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Ubermuda\AdminBundle\Listing\ListPageRequest;

final class ListPageRequestTest extends TestCase
{
    public function testParsesValidParameters(): void
    {
        $request = new Request(['page' => '3', 'sort' => 'name', 'dir' => 'asc']);

        $listPageRequest = ListPageRequest::fromRequest($request, ['name', 'createdAt'], 'createdAt');

        self::assertSame(3, $listPageRequest->page);
        self::assertSame('name', $listPageRequest->sort);
        self::assertSame('asc', $listPageRequest->dir);
    }

    public function testClampsPageToMinimumOfOne(): void
    {
        $request = new Request(['page' => '0']);

        $listPageRequest = ListPageRequest::fromRequest($request, ['name'], 'name');

        self::assertSame(1, $listPageRequest->page);
    }

    public function testClampsNegativePageToOne(): void
    {
        $request = new Request(['page' => '-5']);

        $listPageRequest = ListPageRequest::fromRequest($request, ['name'], 'name');

        self::assertSame(1, $listPageRequest->page);
    }

    public function testFallsBackToDefaultSortWhenNotInAllowlist(): void
    {
        $request = new Request(['sort' => 'unknown']);

        $listPageRequest = ListPageRequest::fromRequest($request, ['name', 'createdAt'], 'createdAt');

        self::assertSame('createdAt', $listPageRequest->sort);
    }

    public function testConstrainsDirectionToAscOrDesc(): void
    {
        $request = new Request(['dir' => 'sideways']);

        $listPageRequest = ListPageRequest::fromRequest($request, ['name'], 'name', 'asc');

        self::assertSame('asc', $listPageRequest->dir);
    }

    public function testLowercasesDirection(): void
    {
        $request = new Request(['dir' => 'DESC']);

        $listPageRequest = ListPageRequest::fromRequest($request, ['name'], 'name');

        self::assertSame('desc', $listPageRequest->dir);
    }

    public function testUsesDefaultsWhenParametersMissing(): void
    {
        $request = new Request();

        $listPageRequest = ListPageRequest::fromRequest($request, ['name'], 'name');

        self::assertSame(1, $listPageRequest->page);
        self::assertSame('name', $listPageRequest->sort);
        self::assertSame('desc', $listPageRequest->dir);
    }
}
