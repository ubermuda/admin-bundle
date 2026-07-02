<?php

namespace Ubermuda\AdminBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Ubermuda\AdminBundle\Menu\AdminMenuItemInterface;

class UbermudaAdminBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('brand_label')->defaultValue('Admin')->end()
                ->scalarNode('app_route')->defaultValue('app_dashboard')
                    ->info('Route the "Back to app" link points at.')->end()
                ->scalarNode('importmap_entry')->defaultValue('app')
                    ->info('importmap() entry rendered in the admin layout head.')->end()
                ->scalarNode('theme')->defaultNull()
                    ->info('Rendered as the data-theme attribute on <html>; omitted when null.')->end()
                ->scalarNode('body_class')->defaultValue('')
                    ->info('Extra class(es) appended to <body>, e.g. an app font class.')->end()
            ->end();
    }

    // No prependExtension: TwigBundle already registers this bundle's templates/
    // dir under @UbermudaAdmin. A manual twig.paths prepend would sit AHEAD of the
    // app's templates/bundles/UbermudaAdminBundle/ override dir in the loader path
    // order, silently disabling app template overrides (e.g. the head_fonts block).

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->setParameter('ubermuda_admin.brand_label', $config['brand_label']);
        $builder->setParameter('ubermuda_admin.app_route', $config['app_route']);
        $builder->setParameter('ubermuda_admin.importmap_entry', $config['importmap_entry']);
        $builder->setParameter('ubermuda_admin.theme', $config['theme']);
        $builder->setParameter('ubermuda_admin.body_class', $config['body_class']);

        $builder->registerForAutoconfiguration(AdminMenuItemInterface::class)
            ->addTag('app.admin_menu_item');

        $container->import('../config/services.php');
    }
}
