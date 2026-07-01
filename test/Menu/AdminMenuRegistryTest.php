<?php

namespace Ubermuda\AdminBundle\Test\Menu;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Ubermuda\AdminBundle\Menu\AdminMenuItemInterface;
use Ubermuda\AdminBundle\Menu\AdminMenuRegistry;

final class AdminMenuRegistryTest extends TestCase
{
    public function testItemsAreSortedByPriorityDescending(): void
    {
        $high = $this->menuItem('high', 100);
        $medium = $this->menuItem('medium', 90);
        $low = $this->menuItem('low', 60);

        // Deliberately unsorted input order.
        $registry = new AdminMenuRegistry(new ArrayIterator([$medium, $low, $high]));

        $items = $registry->items();

        self::assertSame([$high, $medium, $low], $items);
        self::assertSame([100, 90, 60], array_map(
            static fn (AdminMenuItemInterface $item) => $item->getPriority(),
            $items,
        ));
    }

    private function menuItem(string $label, int $priority): AdminMenuItemInterface
    {
        return new class($label, $priority) implements AdminMenuItemInterface {
            public function __construct(
                private readonly string $label,
                private readonly int $priority,
            ) {
            }

            public function getLabel(): string
            {
                return $this->label;
            }

            public function getIcon(): string
            {
                return 'circle';
            }

            public function getRouteName(): string
            {
                return 'app_admin_'.$this->label;
            }

            public function getActiveRoutePrefix(): string
            {
                return 'app_admin_'.$this->label;
            }

            public function getPriority(): int
            {
                return $this->priority;
            }
        };
    }
}
