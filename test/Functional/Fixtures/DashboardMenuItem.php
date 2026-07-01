<?php

namespace Ubermuda\AdminBundle\Test\Functional\Fixtures;

use Ubermuda\AdminBundle\Menu\AdminMenuItemInterface;

final class DashboardMenuItem implements AdminMenuItemInterface
{
    public function getLabel(): string
    {
        return 'Dashboard';
    }

    public function getIcon(): string
    {
        return 'layout-dashboard';
    }

    public function getRouteName(): string
    {
        return 'app_dashboard';
    }

    public function getActiveRoutePrefix(): string
    {
        return 'app_dashboard';
    }

    public function getPriority(): int
    {
        return 100;
    }
}
