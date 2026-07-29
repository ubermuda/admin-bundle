<?php

namespace Ubermuda\AdminBundle\Menu;

/**
 * Implement instead of AdminMenuItemInterface to declare that Turbo must not
 * prefetch this item's link on hover. The interface carries no methods —
 * implementing it is the whole statement.
 *
 * The admin layout opts into Turbo, which prefetches a link's target as soon as
 * the pointer rests on it. For an ordinary page that is free speed, but for a
 * page whose controller does real work merely to render — outbound network
 * probes, an expensive report, anything with a side effect — it means hovering
 * the sidebar entry triggers that work with no user intent behind it. The
 * layout renders data-turbo-prefetch="false" on such an item's anchor.
 *
 * Whether a page is safe to prefetch is a property of its controller's code,
 * not of the current request, so there is nothing for an item to decide at
 * runtime and no method to decide it with. Items that do not implement this
 * interface are prefetched, as before.
 */
interface NonPrefetchableAdminMenuItem extends AdminMenuItemInterface
{
}
