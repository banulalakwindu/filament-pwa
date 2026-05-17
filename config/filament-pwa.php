<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Enable PWA / web push integration
    |--------------------------------------------------------------------------
    */
    'enabled' => env('FILAMENT_PWA_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Filament panel id this plugin targets (for scoped render hooks)
    |--------------------------------------------------------------------------
    */
    'panel_id' => env('FILAMENT_PWA_PANEL_ID', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Panel URL path segment (no leading slash; used by bottom-nav active state)
    |--------------------------------------------------------------------------
    */
    'panel_path' => env('FILAMENT_PWA_PANEL_PATH', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | HTTP route prefix (relative to application root, no leading slash)
    |--------------------------------------------------------------------------
    | Example: admin/api → routes registered as /admin/api/...
    */
    'route_prefix' => env('FILAMENT_PWA_ROUTE_PREFIX', 'admin/api'),

    /*
    |--------------------------------------------------------------------------
    | Middleware applied to PWA API routes
    |--------------------------------------------------------------------------
    */
    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | VAPID keys (web push)
    |--------------------------------------------------------------------------
    | Falls back to services.vapid.* when null.
    */
    'vapid' => [
        'subject' => env('FILAMENT_PWA_VAPID_SUBJECT', env('VAPID_SUBJECT', 'mailto:support@example.com')),
        'public_key' => env('FILAMENT_PWA_PUBLIC_KEY', env('VAPID_PUBLIC_KEY')),
        'private_key' => env('FILAMENT_PWA_PRIVATE_KEY', env('VAPID_PRIVATE_KEY')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Badge count provider (required for badge API + default push payload)
    |--------------------------------------------------------------------------
    | Must implement Banulakwin\FilamentPwa\Contracts\BadgeCountProvider
    */
    'badge_count_provider' => env('FILAMENT_PWA_BADGE_COUNT_PROVIDER'),

    /*
    |--------------------------------------------------------------------------
    | Web push recipients
    |--------------------------------------------------------------------------
    | custom: set web_push_recipient_resolver class implementing
    | Banulakwin\FilamentPwa\Contracts\WebPushRecipientResolver
    |
    | config: use notifiable_model + recipient_criteria.where (associative array)
    |
    | query: notifiable_model must implement static webPushRecipientQuery(): Builder
    | (Banulakwin\FilamentPwa\Contracts\DefinesWebPushRecipientsQuery)
    */
    'recipient_strategy' => env('FILAMENT_PWA_RECIPIENT_STRATEGY', 'config'),

    'notifiable_model' => env('FILAMENT_PWA_NOTIFIABLE_MODEL', 'App\Models\User'),

    'recipient_criteria' => [
        'where' => [
            // 'is_admin' => true,
        ],
    ],

    'web_push_recipient_resolver' => env('FILAMENT_PWA_WEB_PUSH_RECIPIENT_RESOLVER'),

    /*
    |--------------------------------------------------------------------------
    | Cache key prefix (dedupe + rate limit in WebPushBroadcaster)
    |--------------------------------------------------------------------------
    */
    'cache_key_prefix' => env('FILAMENT_PWA_CACHE_PREFIX', 'filament-pwa-push'),

    /*
    |--------------------------------------------------------------------------
    | Defaults for notifications + client
    |--------------------------------------------------------------------------
    */
    'default_open_url' => env('FILAMENT_PWA_DEFAULT_OPEN_URL', '/admin'),

    'default_notification_icon' => env('FILAMENT_PWA_DEFAULT_NOTIFICATION_ICON', '/favicon/web-app-manifest-192x192.png'),

    /*
    |--------------------------------------------------------------------------
    | PWA manifest + meta (Filament head)
    |--------------------------------------------------------------------------
    */
    'app_name' => env('FILAMENT_PWA_APP_NAME', 'Admin'),

    'app_short_name' => env('FILAMENT_PWA_APP_SHORT_NAME', 'Admin'),

    'manifest_path' => env('FILAMENT_PWA_MANIFEST_PATH', '/favicon/admin-site.webmanifest'),

    'apple_mobile_web_app_title' => env('FILAMENT_PWA_APPLE_TITLE'),

    'theme_color' => env('FILAMENT_PWA_THEME_COLOR', '#ffffff'),

    'background_color' => env('FILAMENT_PWA_BACKGROUND_COLOR', '#ffffff'),

    /*
    |--------------------------------------------------------------------------
    | Public asset paths (published stubs)
    |--------------------------------------------------------------------------
    */
    'service_worker_url' => env('FILAMENT_PWA_SERVICE_WORKER_URL', '/sw.js'),

    'favicon' => [
        'png_96' => '/favicon/favicon-96x96.png',
        'svg' => '/favicon/favicon.svg',
        'ico' => '/favicon/favicon.ico',
        'apple_touch' => '/favicon/apple-touch-icon.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Client script (sessionStorage keys, SW message type)
    |--------------------------------------------------------------------------
    */
    'client' => [
        'push_synced_storage_key' => env('FILAMENT_PWA_PUSH_SYNCED_KEY', 'filament-pwa-push-server-synced'),
        'push_sync_banner_dismissed_key' => env('FILAMENT_PWA_PUSH_BANNER_DISMISSED_KEY', 'filament-pwa-push-server-sync-dismissed'),
        'app_badge_message_type' => env('FILAMENT_PWA_APP_BADGE_MESSAGE_TYPE', 'FILAMENT_PWA_APP_BADGE'),
    ],
];
