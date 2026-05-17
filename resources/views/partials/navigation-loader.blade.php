@php
    /** @var string $section One of: head, body, scripts */
    /** @var string $panelPath e.g. /admin */
@endphp

@if ($section === 'head')
    <style>
        .mlb-filament-nav-loading #mlb-filament-nav-overlay {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        #mlb-filament-nav-overlay {
            position: fixed;
            inset: 0;
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
            background: rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(2px);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.15s ease-out, visibility 0.15s ease-out;
        }

        .dark #mlb-filament-nav-overlay {
            background: rgba(0, 0, 0, 0.45);
        }

        #mlb-filament-nav-overlay .mlb-filament-nav-spinner {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            border: 3px solid rgba(255, 255, 255, 0.25);
            border-top-color: rgba(59, 130, 246, 0.95);
            animation: mlb-filament-nav-spin 0.7s linear infinite;
        }

        @keyframes mlb-filament-nav-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <script>
        (function () {
            try {
                if (sessionStorage.getItem('mlb_filament_nav_pending') === '1') {
                    document.documentElement.classList.add('mlb-filament-nav-loading');
                }
            } catch (e) {}
        })();
    </script>
@elseif ($section === 'body')
    <div id="mlb-filament-nav-overlay" aria-live="polite" aria-busy="true" role="status">
        <div class="mlb-filament-nav-spinner" aria-hidden="true"></div>
    </div>
@elseif ($section === 'scripts')
    <script>
        (function () {
            var KEY = 'mlb_filament_nav_pending';
            var CLASS = 'mlb-filament-nav-loading';
            var panelPath = @json($panelPath);

            function show() {
                document.documentElement.classList.add(CLASS);
            }

            function hide() {
                try {
                    sessionStorage.removeItem(KEY);
                } catch (e) {}
                document.documentElement.classList.remove(CLASS);
            }

            window.addEventListener('pageshow', function () {
                hide();
            });

            document.addEventListener(
                'click',
                function (e) {
                    var a = e.target && e.target.closest && e.target.closest('a[href]');
                    if (!a) {
                        return;
                    }
                    if (e.defaultPrevented) {
                        return;
                    }
                    if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
                        return;
                    }
                    if (a.target === '_blank' || a.hasAttribute('download')) {
                        return;
                    }
                    var hrefAttr = a.getAttribute('href');
                    if (!hrefAttr || hrefAttr.startsWith('#') || hrefAttr.toLowerCase().startsWith('javascript:')) {
                        return;
                    }
                    var u;
                    try {
                        u = new URL(a.href, location.href);
                    } catch (err) {
                        return;
                    }
                    if (u.origin !== location.origin) {
                        return;
                    }
                    if (u.pathname !== panelPath && !u.pathname.startsWith(panelPath + '/')) {
                        return;
                    }
                    try {
                        sessionStorage.setItem(KEY, '1');
                    } catch (err2) {}
                    show();
                },
                true,
            );

            document.addEventListener('livewire:navigating', function () {
                show();
            });
            document.addEventListener('livewire:navigated', function () {
                hide();
            });
        })();
    </script>
@endif
