<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa;

use Banulakwin\FilamentPwa\Contracts\WebPushRecipientResolver;
use Banulakwin\FilamentPwa\Notifications\WebPushAlertNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

final class WebPushBroadcaster
{
    /**
     * @param  array{
     *     url?: string,
     *     type?: string,
     *     priority?: 'low'|'normal'|'high',
     *     icon?: string,
     *     badgeCount?: int|null,
     *     dedupeKey?: string,
     *     dedupeTtlSeconds?: int,
     *     rateLimitKey?: string,
     *     rateLimitWindowSeconds?: int,
     *     rateLimitMaxCount?: int
     * }  $data
     */
    public static function send(string $title, string $body, array $data = []): void
    {
        $prefix = config('filament-pwa.cache_key_prefix', 'filament-pwa-push');

        $dedupeKey = $data['dedupeKey'] ?? null;
        if (is_string($dedupeKey) && $dedupeKey !== '') {
            $ttlSeconds = max(1, (int) ($data['dedupeTtlSeconds'] ?? 120));
            $cacheKey = "{$prefix}-dedupe:" . $dedupeKey;

            if (! Cache::add($cacheKey, true, now()->addSeconds($ttlSeconds))) {
                return;
            }
        }

        $rateLimitKey = $data['rateLimitKey'] ?? null;
        if (is_string($rateLimitKey) && $rateLimitKey !== '') {
            $windowSeconds = max(1, (int) ($data['rateLimitWindowSeconds'] ?? 300));
            $maxCount = max(1, (int) ($data['rateLimitMaxCount'] ?? 5));
            $counterCacheKey = "{$prefix}-rate:" . $rateLimitKey;
            $existingCount = Cache::get($counterCacheKey);

            if (is_numeric($existingCount) && (int) $existingCount >= $maxCount) {
                return;
            }

            if (! is_numeric($existingCount)) {
                Cache::put($counterCacheKey, 1, now()->addSeconds($windowSeconds));
            } else {
                Cache::increment($counterCacheKey);
            }
        }

        $recipients = app(WebPushRecipientResolver::class)->resolve();
        if ($recipients->isEmpty()) {
            return;
        }

        $defaultUrl = (string) config('filament-pwa.default_open_url', '/admin');
        $defaultIcon = (string) config('filament-pwa.default_notification_icon', '/favicon/web-app-manifest-192x192.png');

        Notification::send(
            $recipients,
            new WebPushAlertNotification(
                title: $title,
                body: $body,
                url: $data['url'] ?? $defaultUrl,
                type: $data['type'] ?? 'admin-flow',
                priority: $data['priority'] ?? 'normal',
                icon: $data['icon'] ?? $defaultIcon,
                badgeCount: $data['badgeCount'] ?? null,
            ),
        );
    }
}
