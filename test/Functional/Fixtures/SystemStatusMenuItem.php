<?php

namespace Ubermuda\AdminBundle\Test\Functional\Fixtures;

use Ubermuda\AdminBundle\Menu\PrefetchableAdminMenuItem;

/**
 * Stands in for the case the opt-out exists for: a page whose controller does
 * real work (network probes) just to render, so hovering its nav entry must not
 * trigger it.
 */
final class SystemStatusMenuItem implements PrefetchableAdminMenuItem
{
    public function getLabel(): string
    {
        return 'System status';
    }

    public function getIcon(): string
    {
        return 'activity';
    }

    public function getRouteName(): string
    {
        return 'app_admin_system_status';
    }

    public function getActiveRoutePrefix(): string
    {
        return 'app_admin_system_status';
    }

    public function getPriority(): int
    {
        return 50;
    }

    public function shouldPrefetch(): bool
    {
        return false;
    }
}
