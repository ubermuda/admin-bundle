<?php

namespace Ubermuda\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class AdminGlobalsExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private string $brandLabel,
        private string $appRoute,
        private string $importmapEntry,
    ) {
    }

    /** @return array<string, string> */
    public function getGlobals(): array
    {
        return [
            'admin_brand_label' => $this->brandLabel,
            'admin_app_route' => $this->appRoute,
            'admin_importmap_entry' => $this->importmapEntry,
        ];
    }
}
