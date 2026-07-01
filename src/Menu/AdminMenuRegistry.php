<?php

namespace Ubermuda\AdminBundle\Menu;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class AdminMenuRegistry
{
    /** @param iterable<AdminMenuItemInterface> $items */
    public function __construct(
        #[AutowireIterator('app.admin_menu_item')]
        private iterable $items,
    ) {
    }

    /** @return list<AdminMenuItemInterface> */
    public function items(): array
    {
        $items = iterator_to_array($this->items, false);
        usort($items, static fn (AdminMenuItemInterface $a, AdminMenuItemInterface $b) => $b->getPriority() <=> $a->getPriority());

        return $items;
    }
}
