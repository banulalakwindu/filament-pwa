<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;

final class FilamentPwaPlugin implements Plugin
{
    public static function make(): self
    {
        return new self;
    }

    public function getId(): string
    {
        return 'banulakwin-filament-pwa';
    }

    public function register(Panel $panel): void
    {
        if (! config('filament-pwa.enabled', true)) {
            return;
        }

        $panelPath = '/' . mb_ltrim($panel->getPath(), '/');

        // Must register hooks here, not in boot(): Panel::boot() calls registerRenderHooks()
        // before plugin->boot(), so hooks added in boot() never reach FilamentView.
        $panel->renderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => view('filament-pwa::components.pwa-head', [
                'panelPath' => $panelPath,
            ])->render(),
        );

        $panel->renderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => view('filament-pwa::partials.navigation-loader', [
                'section' => 'head',
                'panelPath' => $panelPath,
            ])->render(),
        );

        $panel->renderHook(
            PanelsRenderHook::BODY_START,
            fn (): string => view('filament-pwa::partials.navigation-loader', [
                'section' => 'body',
                'panelPath' => $panelPath,
            ])->render(),
        );

        $panel->renderHook(
            PanelsRenderHook::SCRIPTS_AFTER,
            fn (): string => view('filament-pwa::partials.navigation-loader', [
                'section' => 'scripts',
                'panelPath' => $panelPath,
            ])->render(),
        );

        $panel->renderHook(
            PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
            fn (): string => view('filament-pwa::hooks.mobile-global-search-trigger')->render(),
        );
    }

    public function boot(Panel $panel): void {}
}
