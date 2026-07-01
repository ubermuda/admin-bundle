<?php

namespace Ubermuda\AdminBundle\Menu;

interface AdminMenuItemInterface
{
    public function getLabel(): string;

    /** lucide icon name, e.g. "users" (rendered as ux:icon "lucide:{name}"). */
    public function getIcon(): string;

    public function getRouteName(): string;

    /** Route-name prefix used for the active-state highlight (`_route starts with`). */
    public function getActiveRoutePrefix(): string;

    public function getPriority(): int;
}
