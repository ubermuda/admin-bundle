<?php

namespace Ubermuda\AdminBundle\Test\Functional;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\UX\Icons\UXIconsBundle;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use Ubermuda\AdminBundle\Test\Functional\Fixtures\DashboardMenuItem;
use Ubermuda\AdminBundle\UbermudaAdminBundle;

final class AdminTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new SecurityBundle(),
            new UXIconsBundle(),
            new TwigComponentBundle(),
            new UbermudaAdminBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/ubermuda-admin/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/ubermuda-admin/log';
    }

    protected function configureContainer(ContainerConfigurator $container, LoaderInterface $loader): void
    {
        $container->extension('framework', [
            'secret' => 'test',
            'test' => true,
            'csrf_protection' => true,
            'http_method_override' => false,
            'handle_all_throwables' => true,
            'php_errors' => ['log' => true],
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ]);

        $container->extension('twig', [
            'strict_variables' => true,
            // Register the fixture templates so the functional test can render
            // real, loader-based files (required for the ux-twig-component lexer
            // to preprocess `<twig:...>` tags).
            'paths' => [__DIR__.'/Fixtures/templates' => 'Test'],
        ]);

        $container->extension('twig_component', [
            'anonymous_template_directory' => 'components/',
            // No PHP-backed components ship in the bundle; anonymous templates
            // resolve via the twig namespace fallback, so defaults stays empty.
            'defaults' => [],
        ]);

        // ux-icons resolves `lucide:*` from the Iconify API by default; ignore
        // missing icons so the admin nav renders without a network round-trip.
        $container->extension('ux_icons', [
            'ignore_not_found' => true,
        ]);

        $container->extension('ubermuda_admin', [
            'theme' => 'test-theme',
            'body_class' => 'font-test',
        ]);

        $container->extension('security', [
            'providers' => ['in_memory' => ['memory' => null]],
            'firewalls' => ['main' => ['lazy' => true]],
        ]);

        // The bundle's instanceof/tag rule is file-local to its own services.php,
        // so the fixture menu item must be tagged explicitly here.
        $container->services()
            ->set(DashboardMenuItem::class)
            ->tag('app.admin_menu_item');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // Stub route so path('app_dashboard') resolves in the admin layout.
        $routes->add('app_dashboard', '/')->methods(['GET']);
    }
}
