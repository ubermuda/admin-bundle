<?php

namespace Ubermuda\AdminBundle\Menu;

/**
 * Implement instead of AdminMenuItemInterface on a menu item that needs to say
 * whether Turbo may prefetch its link on hover.
 *
 * The admin layout opts into Turbo, which prefetches a link's target as soon as
 * the pointer rests on it. For an ordinary page that is free speed, but for a
 * page whose controller does real work merely to render — outbound network
 * probes, an expensive report, anything with a side effect — it means hovering
 * the sidebar entry triggers that work with no user intent behind it. Such an
 * item returns false and the layout renders data-turbo-prefetch="false" on its
 * anchor.
 *
 * Items that do not implement this interface are prefetched, as before.
 */
interface PrefetchableAdminMenuItem extends AdminMenuItemInterface
{
    /** False suppresses Turbo's hover prefetch for this item's link. */
    public function shouldPrefetch(): bool;
}
