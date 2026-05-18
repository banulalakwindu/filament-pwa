# Filament PWA (`banulakwin/filament-pwa`)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/banulakwin/filament-pwa.svg?style=flat-square)](https://packagist.org/packages/banulakwin/filament-pwa)
[![Tests](https://github.com/banulalakwindu/filament-pwa/actions/workflows/tests.yml/badge.svg)](https://github.com/banulalakwindu/filament-pwa/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/banulakwin/filament-pwa.svg?style=flat-square)](https://packagist.org/packages/banulakwin/filament-pwa)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

Filament panel PWA support: web push (VAPID + service worker), app badge sync, deduplicated admin broadcasts, mobile global search trigger, navigation loading overlay, and optional mobile bottom navigation view override.

---

## Requirements

- PHP `^8.4`
- Laravel `^11.0|^12.0|^13.0`
- Filament `^5.0`

---

## Installation

```bash
composer require banulakwin/filament-pwa
```

Or run the install command:

```bash
php artisan filament-pwa:install
```

This publishes config, service worker, webmanifest, and mobile bottom nav override.

---

## Configure

### 1. VAPID Keys

Set in `.env`:

```env
VAPID_SUBJECT=mailto:support@example.com
VAPID_PUBLIC_KEY=your_public_key
VAPID_PRIVATE_KEY=your_private_key
```

Or use `FILAMENT_PWA_*` prefixed overrides. Values also merge from `config/services.php` `vapid` if present.

### 2. Badge Count

Implement `Banulakwin\FilamentPwa\Contracts\BadgeCountProvider` and set:

```env
FILAMENT_PWA_BADGE_COUNT_PROVIDER=App\Services\MyBadgeCountProvider
```

### 3. Recipients

Default strategy is `config`:

```env
FILAMENT_PWA_NOTIFIABLE_MODEL=App\Models\User
```

Set `recipient_criteria.where` in config (e.g. `['is_admin' => true]`).

Alternatively use `recipient_strategy=query` with `DefinesWebPushRecipientsQuery` on your model, or bind a custom `WebPushRecipientResolver`.

### 4. Notifiable Model

Add the trait to your user model:

```php
use Banulakwin\FilamentPwa\Concerns\HasWebPushSubscriptions;

class User extends Authenticatable
{
    use HasWebPushSubscriptions;
}
```

### 5. Panel Registration

```php
use Banulakwin\FilamentPwa\FilamentPwaPlugin;

->plugins([
    FilamentPwaPlugin::make(),
])
```

### 6. Optional panel plugins

These are **not** required by Composer; install them in your app when you use the related features:

```bash
composer require charrafimed/global-search-modal hammadzafar05/mobile-bottom-nav
```

```php
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use HammadZafar05\MobileBottomNav\MobileBottomNav;

->plugins([
    FilamentPwaPlugin::make(),
    GlobalSearchModalPlugin::make(),
    MobileBottomNav::make()->items([/* ... */]),
])
```

Run `php artisan filament-pwa:install` (or publish tag `filament-pwa-mobile-bottom-nav`) for the glass bottom nav Blade override.

### 7. PWA appearance

```env
FILAMENT_PWA_THEME_COLOR=#ffffff
FILAMENT_PWA_BACKGROUND_COLOR=#ffffff
FILAMENT_PWA_APPLE_STATUS_BAR_STYLE=default
```

After `filament-pwa:install`, edit `public/favicon/admin-site.webmanifest` so `theme_color` / `background_color` match your brand. The status bar meta tag reads from config automatically.

---

## Sending Pushes

```php
use Banulakwin\FilamentPwa\WebPushBroadcaster;

WebPushBroadcaster::send('Title', 'Body', [
    'url' => '/admin/some-resource/1',
    'type' => 'contact-request',
    'priority' => 'normal',
    'dedupeKey' => 'contact-request:' . $id,
    'dedupeTtlSeconds' => 300,
]);
```

---

## Routes

The package registers `admin/api` routes (configurable via `route_prefix`):

| Route | Method | Description |
|-------|--------|-------------|
| `filament-pwa.badge-count` | GET | Returns badge count JSON |
| `filament-pwa.push-subscription.store` | POST | Register push subscription |
| `filament-pwa.push-subscription.destroy` | DELETE | Remove push subscription |

---

## Publish Tags

| Tag | Description |
|-----|-------------|
| `filament-pwa-config` | Config file |
| `filament-pwa-assets` | Service worker + webmanifest |
| `filament-pwa-views` | Blade views |
| `filament-pwa-mobile-bottom-nav` | Mobile bottom nav override |

---

## Testing

```bash
composer test          # Run PHPUnit
composer pint          # Fix code style
composer phpstan       # Static analysis
composer quality       # Run all (pint + phpstan + test)
```

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for details.

---

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/your-feature`)
3. Run `composer quality` to ensure tests and style pass
4. Commit and push
5. Open a pull request

---

## Package layout (reference)

```
src/
  FilamentPwaPlugin.php
  FilamentPwaServiceProvider.php
  WebPushBroadcaster.php
  Concerns/
    HasWebPushSubscriptions.php
  Console/
    InstallFilamentPwaCommand.php
  Contracts/
    BadgeCountProvider.php
    WebPushRecipientResolver.php
    DefinesWebPushRecipientsQuery.php
  Http/Controllers/
    BadgeCountController.php
    PushSubscriptionController.php
  Models/
    PushSubscription.php
  Notifications/
    WebPushAlertNotification.php
    Channels/
      MinishlinkWebPushChannel.php
  Resolvers/
    ConfigWebPushRecipientResolver.php
config/
  filament-pwa.php
database/
  migrations/
    2026_04_17_150000_create_push_subscriptions_table.php
resources/
  stubs/
    sw.js
    admin-site.webmanifest
  views/
    components/pwa-head.blade.php
    hooks/mobile-global-search-trigger.blade.php
    partials/*.blade.php
    vendor/mobile-bottom-nav/bottom-navigation.blade.php
```

---

## License

MIT — see [LICENSE](LICENSE) for details.
