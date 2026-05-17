@php
    use Filament\Support\Enums\IconSize;
    use function Filament\Support\generate_href_html;
    use function Filament\Support\generate_icon_html;

    $panelSegment = (string) config('filament-pwa.panel_path', 'admin');
@endphp

<style data-navigate-track>
    .fi-bottom-nav {
        position: fixed;
        bottom: calc(1rem + env(safe-area-inset-bottom, 0px));
        left: 1rem;
        right: 1rem;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: space-around;
        height: 4rem;
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(50px) saturate(250%);
        -webkit-backdrop-filter: blur(50px) saturate(250%);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.5),
            inset 0 -1px 0 rgba(255, 255, 255, 0.1),
            0 10px 40px -10px rgba(0, 0, 0, 0.2);
        padding: 0 0.5rem;
    }

    .dark .fi-bottom-nav {
        background: rgba(15, 15, 15, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.1),
            0 10px 40px -10px rgba(0, 0, 0, 0.5);
    }

    .fi-bottom-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        gap: 0.25rem;
        padding: 0.5rem 0;
        border: none;
        background: transparent;
        text-decoration: none;
        cursor: pointer;
        position: relative;
        -webkit-tap-highlight-color: transparent;
        color: var(--gray-500);
        margin: 0.25rem;
        border-radius: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dark .fi-bottom-nav-item {
        color: var(--gray-400);
    }

    .fi-bottom-nav-item.fi-active {
        color: var(--primary-600);
        background: rgba(255, 255, 255, 0.4);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.6),
            0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .dark .fi-bottom-nav-item.fi-active {
        color: var(--primary-400);
        background: rgba(255, 255, 255, 0.1);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.1),
            0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .fi-bottom-nav-label {
        font-size: 0.625rem;
        line-height: 1;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        text-align: center;
        padding: 0 0.25rem;
    }

    .fi-bottom-nav-icon-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fi-bottom-nav-badge {
        position: absolute;
        top: -0.25rem;
        right: -0.5rem;
        min-width: 1rem;
        height: 1rem;
        border-radius: 9999px;
        color: #fff;
        font-size: 0.625rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 0.25rem;
        line-height: 1;
    }

    .fi-bottom-nav-badge-dot {
        position: absolute;
        top: -0.125rem;
        right: -0.125rem;
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 9999px;
    }

    @media (min-width: 1024px) {
        .fi-bottom-nav {
            display: none !important;
        }

        .fi-main {
            padding-bottom: 0 !important;
        }
    }

    @media (max-width: 1023px) {
        .fi-main {
            padding-bottom: calc(6rem + env(safe-area-inset-bottom, 0px)) !important;
        }
    }
</style>

<nav
    x-data="{}"
    x-show="! $store.sidebar.isOpen"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fi-bottom-nav"
    aria-label="Bottom navigation"
>
    @foreach ($items as $item)
        @php
            $isActive = $item->isActiveState();
            $url = $item->getUrl();

            if (! $isActive && $url) {
                $targetPath = ltrim(parse_url($url, PHP_URL_PATH) ?? '', '/');
                $currentPath = request()->path();

                if ($targetPath === $panelSegment) {
                    $isActive = $currentPath === $panelSegment;
                } else {
                    $isActive = $targetPath !== '' && str_starts_with($currentPath, $targetPath);
                }
            }

            $icon = $isActive && $item->getActiveIcon() ? $item->getActiveIcon() : $item->getIcon();
            $badge = $item->getBadge();
            $badgeColor = $item->getBadgeColor() ?? 'primary';
            $badgeCssColor = is_string($badgeColor) ? "var(--{$badgeColor}-500)" : 'var(--primary-500)';
        @endphp

        <a
            {{ generate_href_html($item->getUrl()) }}
            @class(['fi-bottom-nav-item', 'fi-active' => $isActive])
            @if ($isActive) aria-current="page" @endif
        >
            <span class="fi-bottom-nav-icon-wrapper">
                {{ generate_icon_html($icon, size: IconSize::Large) }}

                @if ($badge !== null && $badge !== '')
                    @if (is_numeric($badge))
                        <span class="fi-bottom-nav-badge" style="background-color: {{ $badgeCssColor }}">{{ $badge }}</span>
                    @else
                        <span class="fi-bottom-nav-badge-dot" style="background-color: {{ $badgeCssColor }}"></span>
                    @endif
                @endif
            </span>

            <span class="fi-bottom-nav-label">{{ $item->getLabel() }}</span>
        </a>
    @endforeach

    @if ($moreButtonEnabled)
        <button
            type="button"
            x-on:click="$store.sidebar.open()"
            class="fi-bottom-nav-item"
            aria-label="{{ $moreButtonLabel }}"
        >
            <span class="fi-bottom-nav-icon-wrapper">
                {{ generate_icon_html('heroicon-o-bars-3', size: IconSize::Large) }}
            </span>

            <span class="fi-bottom-nav-label">{{ $moreButtonLabel }}</span>
        </button>
    @endif
</nav>
