<?php

namespace Ubermuda\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Ubermuda\AdminBundle\Menu\AdminMenuItemInterface;
use Ubermuda\AdminBundle\Menu\AdminMenuRegistry;
use Ubermuda\AdminBundle\Menu\PrefetchableAdminMenuItem;

final class AdminMenuExtension extends AbstractExtension
{
    public function __construct(
        private readonly AdminMenuRegistry $registry,
    ) {
    }

    /** @return list<TwigFunction> */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_menu_items', $this->registry->items(...)),
            new TwigFunction('admin_menu_item_prefetch', $this->shouldPrefetch(...)),
        ];
    }

    /**
     * Whether Turbo may prefetch this item's link on hover.
     *
     * The instanceof check lives here rather than in the template because Twig
     * has no instanceof test, and it is what keeps PrefetchableAdminMenuItem
     * optional: an item that does not implement it is prefetched, as before.
     */
    public function shouldPrefetch(AdminMenuItemInterface $item): bool
    {
        return !$item instanceof PrefetchableAdminMenuItem || $item->shouldPrefetch();
    }
}
