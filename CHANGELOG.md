# Changelog

All notable changes to `banulakwin/filament-pwa` will be documented in this file.

## 1.0.1 — 2026-05-18

### Added
- `filament-pwa.apple_mobile_web_app_status_bar_style` config and `FILAMENT_PWA_APPLE_STATUS_BAR_STYLE` env (wired into `pwa-head` meta tag).
- Composer `suggest` entries for `charrafimed/global-search-modal` and `hammadzafar05/mobile-bottom-nav`.

### Changed
- README documents optional panel plugins and PWA theme/status bar configuration.

## 1.0.0 — 2026-05-17

### Added
- Filament plugin `FilamentPwaPlugin` for panel registration with render hooks.
- Web push support via VAPID + service worker (`sw.js`).
- `WebPushBroadcaster::send()` for deduplicated admin broadcasts with rate limiting.
- `push_subscriptions` table with morph relationship to notifiable models.
- `HasWebPushSubscriptions` trait for user models.
- `BadgeCountProvider` contract for app badge count API.
- `WebPushRecipientResolver` contract with `ConfigWebPushRecipientResolver` default.
- `DefinesWebPushRecipientsQuery` contract for query-based recipient resolution.
- `MinishlinkWebPushChannel` notification channel.
- Mobile global search trigger hook.
- Navigation loading overlay for SPA transitions.
- Optional mobile bottom navigation view override.
- `filament-pwa:install` artisan command.
- Configurable panel path, route prefix, VAPID keys, and recipient criteria.
- PHPUnit test suite with Orchestra Testbench.
- GitHub Actions CI workflow (tests, Pint, PHPStan).
- Laravel Pint code style configuration.
- PHPStan static analysis (level max).
