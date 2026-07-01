<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$brandLabel', param('ubermuda_admin.brand_label'))
        ->bind('$appRoute', param('ubermuda_admin.app_route'))
        ->bind('$importmapEntry', param('ubermuda_admin.importmap_entry'));

    $services->instanceof(\Ubermuda\AdminBundle\Menu\AdminMenuItemInterface::class)
        ->tag('app.admin_menu_item');

    $services->load('Ubermuda\\AdminBundle\\', __DIR__.'/../src/')
        ->exclude([__DIR__.'/../src/UbermudaAdminBundle.php']);
};
