<style>
    .fi-topbar .mlb-mobile-global-search-trigger-ctn {
        display: none;
    }

    @media (max-width: 1023px) {
        .fi-topbar-open-sidebar-btn {
            display: none !important;
        }

        .fi-topbar-start {
            display: flex !important;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 2.75rem;
            height: 2.75rem;
            padding: 0.45rem;
            box-sizing: border-box;
            border-radius: 9999px;
            -webkit-tap-highlight-color: transparent;
            isolation: isolate;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(50px) saturate(250%);
            -webkit-backdrop-filter: blur(50px) saturate(250%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.5),
                inset 0 -1px 0 rgba(255, 255, 255, 0.1),
                0 10px 40px -10px rgba(0, 0, 0, 0.2);
        }

        .dark .fi-topbar-start {
            background: rgba(15, 15, 15, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.1),
                0 10px 40px -10px rgba(0, 0, 0, 0.5);
        }

        .fi-topbar-start .fi-topbar-collapse-sidebar-btn-ctn {
            display: none !important;
        }

        .fi-topbar-start .fi-logo {
            margin-inline-start: 0 !important;
        }

        .fi-topbar-start .fi-logo img,
        .fi-topbar-start .fi-logo svg {
            max-height: 1.625rem;
            width: auto;
        }

        .fi-topbar-ctn {
            background: transparent !important;
            box-shadow: none !important;
            padding-top: calc(0.5rem + env(safe-area-inset-top, 0px));
            padding-left: 1rem;
            padding-right: 1rem;
            padding-bottom: 0.375rem;
        }

        .fi-topbar {
            min-height: 3.5rem;
            padding-inline: 0;
            column-gap: 0.75rem;
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            --tw-ring-shadow: 0 0 #0000 !important;
            --tw-ring-offset-shadow: 0 0 #0000 !important;
        }

        .fi-topbar .fi-global-search-ctn > .fi-global-search {
            display: none !important;
        }

        .fi-topbar .mlb-mobile-global-search-trigger-ctn {
            display: flex !important;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
        }

        .fi-topbar-end {
            column-gap: 0.5rem;
        }

        .fi-topbar > .fi-topbar-close-sidebar-btn {
            width: 2.75rem !important;
            height: 2.75rem !important;
            min-width: 2.75rem !important;
            min-height: 2.75rem !important;
            border-radius: 9999px !important;
            padding: 0 !important;
            margin-inline-end: 0.5rem !important;
            -webkit-tap-highlight-color: transparent;
            isolation: isolate;
            background: rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(50px) saturate(250%);
            -webkit-backdrop-filter: blur(50px) saturate(250%);
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.5),
                inset 0 -1px 0 rgba(255, 255, 255, 0.1),
                0 10px 40px -10px rgba(0, 0, 0, 0.2) !important;
            --tw-ring-shadow: 0 0 #0000 !important;
        }

        .dark .fi-topbar > .fi-topbar-close-sidebar-btn {
            background: rgba(15, 15, 15, 0.25) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.1),
                0 10px 40px -10px rgba(0, 0, 0, 0.5) !important;
        }

        .fi-topbar .fi-topbar-end .fi-icon-btn,
        .fi-topbar .fi-topbar-database-notifications-btn {
            width: 2.75rem !important;
            height: 2.75rem !important;
            min-width: 2.75rem !important;
            min-height: 2.75rem !important;
            border-radius: 9999px !important;
            padding: 0 !important;
            -webkit-tap-highlight-color: transparent;
            isolation: isolate;
            background: rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(50px) saturate(250%);
            -webkit-backdrop-filter: blur(50px) saturate(250%);
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.5),
                inset 0 -1px 0 rgba(255, 255, 255, 0.1),
                0 10px 40px -10px rgba(0, 0, 0, 0.2) !important;
            --tw-ring-shadow: 0 0 #0000 !important;
        }

        .dark .fi-topbar .fi-topbar-end .fi-icon-btn,
        .dark .fi-topbar .fi-topbar-database-notifications-btn {
            background: rgba(15, 15, 15, 0.25) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.1),
                0 10px 40px -10px rgba(0, 0, 0, 0.5) !important;
        }

        .fi-topbar .fi-user-menu-trigger {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 2.75rem;
            height: 2.75rem;
            min-width: 2.75rem;
            min-height: 2.75rem;
            padding: 0;
            border-radius: 9999px;
            overflow: hidden;
            -webkit-tap-highlight-color: transparent;
            isolation: isolate;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(50px) saturate(250%);
            -webkit-backdrop-filter: blur(50px) saturate(250%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.5),
                inset 0 -1px 0 rgba(255, 255, 255, 0.1),
                0 10px 40px -10px rgba(0, 0, 0, 0.2);
        }
    }
</style>
