<?php

namespace Ubermuda\AdminBundle\Test\Twig;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Ubermuda\AdminBundle\Menu\AdminMenuItemInterface;
use Ubermuda\AdminBundle\Menu\AdminMenuRegistry;
use Ubermuda\AdminBundle\Menu\NonPrefetchableAdminMenuItem;
use Ubermuda\AdminBundle\Twig\AdminMenuExtension;

final class AdminMenuExtensionTest extends TestCase
{
    public function testPlainItemKeepsPrefetchEnabled(): void
    {
        // The backwards-compatibility case: every menu item written before the
        // interface existed keeps the previous behaviour.
        self::assertFalse($this->extension()->isPrefetchDisabled($this->plainItem()));
    }

    public function testItemImplementingTheMarkerHasPrefetchDisabled(): void
    {
        self::assertTrue($this->extension()->isPrefetchDisabled($this->nonPrefetchableItem()));
    }

    public function testThePrefetchFunctionIsExposedToTwig(): void
    {
        $names = array_map(
            static fn ($function) => $function->getName(),
            $this->extension()->getFunctions(),
        );

        self::assertContains('admin_menu_item_prefetch_disabled', $names);
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

    private function nonPrefetchableItem(): NonPrefetchableAdminMenuItem
    {
        return new class implements NonPrefetchableAdminMenuItem {
            public function getLabel(): string
            {
                return 'Not prefetchable';
            }

            public function getIcon(): string
            {
                return 'circle';
            }

            public function getRouteName(): string
            {
                return 'app_admin_not_prefetchable';
            }

            public function getActiveRoutePrefix(): string
            {
                return 'app_admin_not_prefetchable';
            }

            public function getPriority(): int
            {
                return 100;
            }
        };
    }
}
