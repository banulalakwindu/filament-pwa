<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa;

use Banulakwin\FilamentPwa\Contracts\BadgeCountProvider;
use Banulakwin\FilamentPwa\Contracts\WebPushRecipientResolver;
use Banulakwin\FilamentPwa\Http\Controllers\BadgeCountController;
use Banulakwin\FilamentPwa\Http\Controllers\PushSubscriptionController;
use Banulakwin\FilamentPwa\Resolvers\ConfigWebPushRecipientResolver;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

final class FilamentPwaServiceProvider extends ServiceProvider
{
    private function mergeVapidFromServices(): void
    {
        $map = [
            'filament-pwa.vapid.subject' => 'services.vapid.subject',
            'filament-pwa.vapid.public_key' => 'services.vapid.public_key',
            'filament-pwa.vapid.private_key' => 'services.vapid.private_key',
        ];

        foreach ($map as $pwaKey => $servicesKey) {
            $current = Config::get($pwaKey);
            if (($current === null || $current === '') && Config::has($servicesKey)) {
                $fallback = Config::get($servicesKey);
                if ($fallback !== null && $fallback !== '') {
                    Config::set($pwaKey, $fallback);
                }
            }
        }
    }

    private function registerRoutes(): void
    {
        $prefix = mb_trim((string) config('filament-pwa.route_prefix', 'admin/api'), '/');
        /** @var list<string> $middleware */
        $middleware = config('filament-pwa.middleware', ['web', 'auth']);

        Route::middleware($middleware)
            ->prefix($prefix)
            ->name('filament-pwa.')
            ->group(function (): void {
                Route::get('badge-count', [BadgeCountController::class, 'show'])
                    ->name('badge-count');
                Route::post('push-subscription', [PushSubscriptionController::class, 'store'])
                    ->name('push-subscription.store');
                Route::delete('push-subscription', [PushSubscriptionController::class, 'destroy'])
                    ->name('push-subscription.destroy');
            });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-pwa.php', 'filament-pwa');

        $this->app->singleton(WebPushRecipientResolver::class, function ($app): WebPushRecipientResolver {
            $class = config('filament-pwa.web_push_recipient_resolver');
            if (is_string($class) && class_exists($class)) {
                return $app->make($class);
            }

            return new ConfigWebPushRecipientResolver;
        });

        $this->app->singleton(BadgeCountProvider::class, function ($app): BadgeCountProvider {
            $class = config('filament-pwa.badge_count_provider');
            if (! is_string($class) || ! class_exists($class)) {
                throw new RuntimeException(
                    'Configure filament-pwa.badge_count_provider to a class implementing ' . BadgeCountProvider::class,
                );
            }

            return $app->make($class);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/filament-pwa.php' => config_path('filament-pwa.php'),
        ], 'filament-pwa-config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-pwa');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-pwa'),
        ], 'filament-pwa-views');

        $this->publishes([
            __DIR__ . '/../resources/stubs/sw.js' => public_path('sw.js'),
            __DIR__ . '/../resources/stubs/admin-site.webmanifest' => public_path('favicon/admin-site.webmanifest'),
        ], 'filament-pwa-assets');

        $this->publishes([
            __DIR__ . '/../resources/views/vendor/mobile-bottom-nav/bottom-navigation.blade.php' => resource_path('views/vendor/mobile-bottom-nav/bottom-navigation.blade.php'),
        ], 'filament-pwa-mobile-bottom-nav');

        $this->mergeVapidFromServices();

        if (config('filament-pwa.enabled', true)) {
            $this->registerRoutes();
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallFilamentPwaCommand::class,
            ]);
        }
    }
}
