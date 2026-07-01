<?php

namespace Ubermuda\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Ubermuda\AdminBundle\Menu\AdminMenuRegistry;

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
        ];
    }
}
