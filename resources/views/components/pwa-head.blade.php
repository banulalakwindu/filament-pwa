@php
    /** @var string $panelPath e.g. /admin */
    $manifestPath = config('filament-pwa.manifest_path', '/favicon/admin-site.webmanifest');
    $favicon = config('filament-pwa.favicon', []);
    $appleTitle = config('filament-pwa.apple_mobile_web_app_title')
        ?: config('filament-pwa.app_short_name', 'Admin');
@endphp

<link rel="icon" type="image/png" href="{{ $favicon['png_96'] ?? '/favicon/favicon-96x96.png' }}" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="{{ $favicon['svg'] ?? '/favicon/favicon.svg' }}" />
<link rel="shortcut icon" href="{{ $favicon['ico'] ?? '/favicon/favicon.ico' }}" />
<link rel="apple-touch-icon" sizes="180x180" href="{{ $favicon['apple_touch'] ?? '/favicon/apple-touch-icon.png' }}" />
<link rel="manifest" href="{{ $manifestPath }}" />

<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ $appleTitle }}" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="vapid-public-key" content="{{ config('filament-pwa.vapid.public_key') }}">
<script type="application/json" id="filament-pwa-vapid-public-json">{!! json_encode(config('filament-pwa.vapid.public_key'), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES) !!}</script>

@include('filament-pwa::partials.mobile-topbar-styles')

@include('filament-pwa::partials.push-client', ['panelPath' => $panelPath])
