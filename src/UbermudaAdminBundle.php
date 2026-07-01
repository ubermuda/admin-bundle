<?php

namespace Ubermuda\AdminBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

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
            ->end();
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if ($builder->hasExtension('twig')) {
            $builder->prependExtensionConfig('twig', [
                'paths' => [__DIR__.'/../templates' => 'UbermudaAdmin'],
            ]);
        }
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->setParameter('ubermuda_admin.brand_label', $config['brand_label']);
        $builder->setParameter('ubermuda_admin.app_route', $config['app_route']);
        $builder->setParameter('ubermuda_admin.importmap_entry', $config['importmap_entry']);

        $container->import('../config/services.php');
    }
}
