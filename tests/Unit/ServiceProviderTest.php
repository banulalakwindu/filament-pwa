<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Tests\Unit;

use Banulakwin\FilamentPwa\FilamentPwaPlugin;
use Banulakwin\FilamentPwa\FilamentPwaServiceProvider;
use Banulakwin\FilamentPwa\Tests\TestCase;
use Banulakwin\FilamentPwa\WebPushBroadcaster;
use PHPUnit\Framework\Attributes\Test;

class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_has_service_provider(): void
    {
        $this->assertTrue(class_exists(FilamentPwaServiceProvider::class));
    }

    #[Test]
    public function it_has_plugin_class(): void
    {
        $this->assertTrue(class_exists(FilamentPwaPlugin::class));
    }

    #[Test]
    public function it_has_web_push_broadcaster(): void
    {
        $this->assertTrue(class_exists(WebPushBroadcaster::class));
    }

    #[Test]
    public function plugin_can_be_created(): void
    {
        $plugin = FilamentPwaPlugin::make();

        $this->assertInstanceOf(FilamentPwaPlugin::class, $plugin);
        $this->assertEquals('banulakwin-filament-pwa', $plugin->getId());
    }
}
