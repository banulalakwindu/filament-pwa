# Agent guide: `banulakwin/filament-pwa`

## Mental model

- **Client:** Filament head hooks inject manifest links, VAPID meta, mobile topbar CSS, push subscription script, service worker registration, and nav loading overlay. Service worker (`public/sw.js`) handles push display and `FILAMENT_PWA_APP_BADGE` messages.
- **Server:** `push_subscriptions` table (morph to notifiable). API routes for badge JSON and subscribe/unsubscribe. `WebPushBroadcaster` resolves recipients, applies dedupe/rate limits, and sends `WebPushAlertNotification` through `MinishlinkWebPushChannel`.
- **Host-specific:** Who receives pushes (`recipient_criteria` or custom resolver) and badge count (`BadgeCountProvider`). Deep links are plain URL strings from host code (e.g. Filament resource URLs).

## Public API

- `FilamentPwaPlugin::make()` — register on the target panel only.
- `WebPushBroadcaster::send($title, $body, $data = [])` — same payload keys as documented in README.
- `Banulakwin\FilamentPwa\Contracts\BadgeCountProvider`
- `Banulakwin\FilamentPwa\Contracts\WebPushRecipientResolver` / `Resolvers\ConfigWebPushRecipientResolver`
- `Banulakwin\FilamentPwa\Contracts\DefinesWebPushRecipientsQuery` — optional static `webPushRecipientQuery()` when `recipient_strategy` = `query`.
- `Banulakwin\FilamentPwa\Concerns\HasWebPushSubscriptions`

## Do

- Keep `GlobalSearchModalPlugin` and `MobileBottomNav` in the same panel as `FilamentPwaPlugin`.
- Publish `filament-pwa-mobile-bottom-nav` when using the glass bottom nav override; set `panel_path` to match the panel URL segment.
- Align `client.app_badge_message_type` with `sw.js` `postMessage` type (`FILAMENT_PWA_APP_BADGE` by default).

## Do not

- Import `App\*` from the package.
- Register duplicate `admin/api` routes in the host app when using the package defaults.

## Testing & Quality

```bash
composer test          # PHPUnit
composer pint          # Laravel Pint code style fix
composer pint:check    # Pint check only (no fix)
composer phpstan       # PHPStan level max on src/
composer quality       # All: pint + phpstan + test
```

## CI

GitHub Actions runs tests, Pint, and PHPStan on push/PR (`.github/workflows/tests.yml`).
