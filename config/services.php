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
        ->bind('$importmapEntry', param('ubermuda_admin.importmap_entry'))
        ->bind('$theme', param('ubermuda_admin.theme'))
        ->bind('$bodyClass', param('ubermuda_admin.body_class'));

    $services->load('Ubermuda\\AdminBundle\\', __DIR__.'/../src/')
        ->exclude([
            __DIR__.'/../src/UbermudaAdminBundle.php',
            // Registered conditionally in UbermudaAdminBundle::loadExtension --
            // it requires Doctrine, which not every consumer ships.
            __DIR__.'/../src/Security/PromoteAdminUserListener.php',
        ]);
};
