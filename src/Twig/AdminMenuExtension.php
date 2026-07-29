<?php

namespace Ubermuda\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Ubermuda\AdminBundle\Menu\AdminMenuItemInterface;
use Ubermuda\AdminBundle\Menu\AdminMenuRegistry;
use Ubermuda\AdminBundle\Menu\NonPrefetchableAdminMenuItem;

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
            new TwigFunction('admin_menu_item_prefetch_disabled', $this->isPrefetchDisabled(...)),
        ];
    }

    /**
     * Whether the layout must suppress Turbo's hover prefetch for this item.
     *
     * The instanceof check lives here rather than in the template because Twig
     * has no instanceof test, and it is what keeps NonPrefetchableAdminMenuItem
     * optional: an item that does not implement it is prefetched, as before.
     */
    public function isPrefetchDisabled(AdminMenuItemInterface $item): bool
    {
        return $item instanceof NonPrefetchableAdminMenuItem;
    }
}
