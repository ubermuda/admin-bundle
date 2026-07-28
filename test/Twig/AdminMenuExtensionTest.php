<?php

namespace Ubermuda\AdminBundle\Test\Twig;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Ubermuda\AdminBundle\Menu\AdminMenuItemInterface;
use Ubermuda\AdminBundle\Menu\AdminMenuRegistry;
use Ubermuda\AdminBundle\Menu\PrefetchableAdminMenuItem;
use Ubermuda\AdminBundle\Twig\AdminMenuExtension;

final class AdminMenuExtensionTest extends TestCase
{
    public function testItemNotImplementingThePrefetchInterfaceIsPrefetched(): void
    {
        // The backwards-compatibility case: every menu item written before the
        // interface existed keeps the previous behaviour.
        self::assertTrue($this->extension()->shouldPrefetch($this->plainItem()));
    }

    public function testItemOptingOutIsNotPrefetched(): void
    {
        self::assertFalse($this->extension()->shouldPrefetch($this->prefetchableItem(false)));
    }

    public function testItemOptingInIsPrefetched(): void
    {
        self::assertTrue($this->extension()->shouldPrefetch($this->prefetchableItem(true)));
    }

    public function testThePrefetchFunctionIsExposedToTwig(): void
    {
        $names = array_map(
            static fn ($function) => $function->getName(),
            $this->extension()->getFunctions(),
        );

        self::assertContains('admin_menu_item_prefetch', $names);
    }

    private function extension(): AdminMenuExtension
    {
        return new AdminMenuExtension(new AdminMenuRegistry(new ArrayIterator([])));
    }

    private function plainItem(): AdminMenuItemInterface
    {
        return new class implements AdminMenuItemInterface {
            public function getLabel(): string
            {
                return 'Plain';
            }

            public function getIcon(): string
            {
                return 'circle';
            }

            public function getRouteName(): string
            {
                return 'app_admin_plain';
            }

            public function getActiveRoutePrefix(): string
            {
                return 'app_admin_plain';
            }

            public function getPriority(): int
            {
                return 100;
            }
        };
    }

    private function prefetchableItem(bool $shouldPrefetch): PrefetchableAdminMenuItem
    {
        return new class($shouldPrefetch) implements PrefetchableAdminMenuItem {
            public function __construct(
                private readonly bool $shouldPrefetch,
            ) {
            }

            public function getLabel(): string
            {
                return 'Prefetchable';
            }

            public function getIcon(): string
            {
                return 'circle';
            }

            public function getRouteName(): string
            {
                return 'app_admin_prefetchable';
            }

            public function getActiveRoutePrefix(): string
            {
                return 'app_admin_prefetchable';
            }

            public function getPriority(): int
            {
                return 100;
            }

            public function shouldPrefetch(): bool
            {
                return $this->shouldPrefetch;
            }
        };
    }
}
