<?php

namespace Ubermuda\AdminBundle\Test\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Twig\Environment;

final class TemplateRenderingTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return AdminTestKernel::class;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // FrameworkBundle::boot() registers an ErrorHandler exception handler that
        // kernel shutdown does not pop; restore it so PHPUnit does not flag the test risky.
        restore_exception_handler();
    }

    private function twig(?string $route = null): Environment
    {
        self::bootKernel();
        $container = self::getContainer();

        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        if (null !== $route) {
            $request->attributes->set('_route', $route);
        }
        $container->get('request_stack')->push($request);

        return $container->get('twig');
    }

    public function testBaseLayoutRendersBrandAndRegisteredMenuItem(): void
    {
        // Pushing _route=app_dashboard also exercises the is-active nav class.
        $html = $this->twig('app_dashboard')->render('@Test/extends_base.html.twig');

        // Brand label (the configured default) proves the base layout + globals.
        self::assertStringContainsString('Admin', $html);
        // Nav <a> for the registered fixture menu item proves admin_menu_items()
        // + the app.admin_menu_item tag + inline nav rendering.
        self::assertMatchesRegularExpression('/<a[^>]*>\s*(?:<svg[^>]*>.*?<\/svg>)?\s*Dashboard/s', $html);
        self::assertStringContainsString('Dashboard', $html);
        // The item's route resolves to '/', and its active prefix matches _route.
        self::assertStringContainsString('is-active', $html);
    }

    public function testConfiguredThemeAndBodyClassRender(): void
    {
        $html = $this->twig('app_dashboard')->render('@Test/extends_base.html.twig');

        // theme config -> data-theme attribute on <html>.
        self::assertStringContainsString('data-theme="test-theme"', $html);
        // body_class config -> appended to the <body> class list.
        self::assertMatchesRegularExpression('/<body[^>]*class="[^"]*font-test/', $html);
        // No font <link>s ship by default -- apps supply them via the head_fonts block.
        self::assertStringNotContainsString('fonts.googleapis.com', $html);
    }

    public function testNamespacedAdminListComponentResolvesAndRenders(): void
    {
        $html = $this->twig()->render('@Test/uses_adminlist.html.twig');

        // The load-bearing proof: `<twig:UbermudaAdmin:AdminList>` resolved to
        // @UbermudaAdmin/components/AdminList.html.twig and rendered the table shell.
        self::assertStringContainsString('<table class="admin-table', $html);
    }
}
