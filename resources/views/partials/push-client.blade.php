@php
    /** @var string $panelPath */
    $pushStoreUrl = route('filament-pwa.push-subscription.store');
    $badgeUrl = route('filament-pwa.badge-count');
    $syncKey = config('filament-pwa.client.push_synced_storage_key');
    $dismissKey = config('filament-pwa.client.push_sync_banner_dismissed_key');
    $badgeMsgType = config('filament-pwa.client.app_badge_message_type');
    $swUrl = config('filament-pwa.service_worker_url', '/sw.js');
@endphp
<script>
(function () {
    var swRegistration = null;
    var pushSyncedStorageKey = @json($syncKey);
    var pushSyncBannerDismissedKey = @json($dismissKey);
    var appBadgeMessageType = @json($badgeMsgType);
    var serviceWorkerUrl = @json($swUrl);
    var pushSubscriptionStoreUrl = @json($pushStoreUrl);
    var badgeCountUrl = @json($badgeUrl);

    function isPushSyncedOk() {
        try {
            return sessionStorage.getItem(pushSyncedStorageKey) === "1";
        } catch (e) {
            return false;
        }
    }

    function markPushSyncedOk() {
        try {
            sessionStorage.setItem(pushSyncedStorageKey, "1");
            sessionStorage.removeItem(pushSyncBannerDismissedKey);
        } catch (e) {}
        var el = document.getElementById("push-server-sync-banner");
        if (el) {
            el.remove();
        }
    }

    function urlBase64ToUint8Array(base64String) {
        var padding = "=".repeat((4 - (base64String.length % 4)) % 4);
        var base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");
        var rawData = window.atob(base64);
        var outputArray = new Uint8Array(rawData.length);
        for (var i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function getVapidPublicKeyRaw() {
        var el = document.getElementById("filament-pwa-vapid-public-json");
        if (el && el.textContent) {
            try {
                var parsed = JSON.parse(el.textContent.trim());
                if (typeof parsed === "string" && parsed.length > 0) {
                    return parsed.trim();
                }
            } catch (e) {}
        }
        var meta = document.querySelector("meta[name=vapid-public-key]");
        return meta && meta.content ? String(meta.content).trim() : "";
    }

    function sendSubscriptionToServer(subscription) {
        var csrf = document.querySelector("meta[name=csrf-token]");
        if (!csrf || !csrf.content) {
            showServerSyncBanner(null, false);
            return;
        }
        var payload = subscription.toJSON();
        fetch(pushSubscriptionStoreUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": csrf.content,
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "same-origin",
            body: JSON.stringify({
                endpoint: payload.endpoint,
                keys: payload.keys,
                content_encoding: payload.contentEncoding || "aesgcm",
            }),
        })
            .then(function (res) {
                if (res.ok) {
                    markPushSyncedOk();
                    return;
                }
                showServerSyncBanner(null, false);
            })
            .catch(function () {
                showServerSyncBanner(null, false);
            });
    }

    function subscribeToPush(registration) {
        var rawKey = getVapidPublicKeyRaw();
        if (!rawKey) {
            if (Notification.permission === "granted") {
                showServerSyncBanner(
                    "Push: VAPID public key is missing or empty. Add keys to .env, then run php artisan config:clear.",
                    true,
                );
            }
            return;
        }

        var applicationServerKey = urlBase64ToUint8Array(rawKey);
        if (applicationServerKey.byteLength !== 65) {
            if (Notification.permission === "granted") {
                showServerSyncBanner(
                    "Push: VAPID public key is invalid. Regenerate keys and run php artisan config:clear.",
                    true,
                );
            }
            return;
        }

        registration.pushManager
            .getSubscription()
            .then(function (existing) {
                if (existing) {
                    sendSubscriptionToServer(existing);
                    return existing;
                }

                return registration.pushManager
                    .subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: applicationServerKey,
                    })
                    .then(function (subscription) {
                        sendSubscriptionToServer(subscription);
                        return subscription;
                    });
            })
            .catch(function () {
                if (Notification.permission === "granted") {
                    showServerSyncBanner(null, false);
                }
            });
    }

    function showServerSyncBanner(message, hideSaveButton) {
        if (document.getElementById("push-server-sync-banner") || isPushSyncedOk()) {
            return;
        }

        if (typeof hideSaveButton === "undefined") {
            hideSaveButton = false;
        }

        var banner = document.createElement("div");
        banner.id = "push-server-sync-banner";
        banner.style.cssText =
            "position:fixed;bottom:140px;left:50%;transform:translateX(-50%);z-index:9998;background:#0f766e;color:#fff;padding:12px 20px;border-radius:12px;display:flex;align-items:center;gap:12px;box-shadow:0 4px 20px rgba(0,0,0,0.3);font-size:14px;font-family:sans-serif;max-width:90vw;";

        var text = document.createElement("span");
        text.textContent =
            message || "Save this browser for admin push alerts.";

        var btn = document.createElement("button");
        btn.textContent = "Save now";
        btn.style.cssText =
            "background:#fff;color:#0f766e;border:none;padding:6px 16px;border-radius:8px;font-weight:600;cursor:pointer;white-space:nowrap;";
        btn.onclick = function () {
            if (swRegistration) {
                subscribeToPush(swRegistration);
            }
        };

        var close = document.createElement("button");
        close.textContent = "\u00d7";
        close.style.cssText =
            "background:none;border:none;color:#fff;font-size:20px;cursor:pointer;padding:0 4px;";
        close.onclick = function () {
            try {
                sessionStorage.setItem(pushSyncBannerDismissedKey, "1");
            } catch (e) {}
            banner.remove();
        };

        banner.appendChild(text);
        if (!hideSaveButton) {
            banner.appendChild(btn);
        }
        banner.appendChild(close);
        document.body.appendChild(banner);
    }

    function scheduleServerSyncBannerIfNeeded() {
        setTimeout(function () {
            if (Notification.permission !== "granted") {
                return;
            }
            if (isPushSyncedOk()) {
                return;
            }
            try {
                if (sessionStorage.getItem(pushSyncBannerDismissedKey) === "1") {
                    return;
                }
            } catch (e) {}
            showServerSyncBanner(null, false);
        }, 2500);
    }

    function applyAppBadgeLocal(rawCount) {
        if (!("setAppBadge" in navigator) && !("clearAppBadge" in navigator)) {
            return;
        }
        var n = parseInt(rawCount, 10);
        if (!Number.isFinite(n) || n < 0) {
            n = 0;
        }
        var p = null;
        if (n > 0) {
            if (typeof navigator.setAppBadge === "function") {
                p = navigator.setAppBadge(n);
            }
        } else if (typeof navigator.clearAppBadge === "function") {
            p = navigator.clearAppBadge();
        } else if (typeof navigator.setAppBadge === "function") {
            p = navigator.setAppBadge(0);
        }
        if (p && typeof p.then === "function") {
            return p.catch(function () {});
        }
    }

    var appBadgeSyncTimer = null;

    function scheduleAppBadgeSync() {
        if (!("setAppBadge" in navigator) && !("clearAppBadge" in navigator)) {
            return;
        }
        if (appBadgeSyncTimer !== null) {
            clearTimeout(appBadgeSyncTimer);
        }
        appBadgeSyncTimer = setTimeout(function () {
            appBadgeSyncTimer = null;
            syncAppBadgeFromServer();
        }, 500);
    }

    function syncAppBadgeFromServer() {
        if (!("setAppBadge" in navigator) && !("clearAppBadge" in navigator)) {
            return;
        }
        fetch(badgeCountUrl, {
            method: "GET",
            credentials: "same-origin",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then(function (res) {
                return res.ok ? res.json() : Promise.reject(new Error("badge"));
            })
            .then(function (data) {
                return applyAppBadgeLocal(data.count);
            })
            .catch(function () {});
    }

    function showNotificationBanner() {
        if (document.getElementById("push-banner")) {
            return;
        }

        var banner = document.createElement("div");
        banner.id = "push-banner";
        banner.style.cssText =
            "position:fixed;bottom:80px;left:50%;transform:translateX(-50%);z-index:9999;background:#1e40af;color:#fff;padding:12px 20px;border-radius:12px;display:flex;align-items:center;gap:12px;box-shadow:0 4px 20px rgba(0,0,0,0.3);font-size:14px;font-family:sans-serif;max-width:90vw;";

        var text = document.createElement("span");
        text.textContent = "Enable notifications to get admin alerts";

        var btn = document.createElement("button");
        btn.textContent = "Enable";
        btn.style.cssText =
            "background:#fff;color:#1e40af;border:none;padding:6px 16px;border-radius:8px;font-weight:600;cursor:pointer;white-space:nowrap;";
        btn.onclick = function () {
            Notification.requestPermission().then(function (permission) {
                banner.remove();
                if (permission === "granted" && swRegistration) {
                    subscribeToPush(swRegistration);
                }
            });
        };

        var close = document.createElement("button");
        close.textContent = "\u00d7";
        close.style.cssText =
            "background:none;border:none;color:#fff;font-size:20px;cursor:pointer;padding:0 4px;";
        close.onclick = function () {
            banner.remove();
        };

        banner.appendChild(text);
        banner.appendChild(btn);
        banner.appendChild(close);
        document.body.appendChild(banner);
    }

    function checkAndPromptPush() {
        if (!("Notification" in window) || !("PushManager" in window)) {
            return;
        }
        if (!swRegistration) {
            return;
        }

        var rawVapid = getVapidPublicKeyRaw();
        if (!rawVapid) {
            if (Notification.permission === "granted") {
                showServerSyncBanner(
                    "Push: VAPID public key is missing or empty. Add keys to .env, then run php artisan config:clear.",
                    true,
                );
            }
            return;
        }

        if (Notification.permission === "granted") {
            subscribeToPush(swRegistration);
            scheduleServerSyncBannerIfNeeded();
            return;
        }

        if (Notification.permission === "denied") {
            return;
        }

        showNotificationBanner();
    }

    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.addEventListener("message", function (event) {
            var d = event.data;
            if (!d || d.type !== appBadgeMessageType) {
                return;
            }
            applyAppBadgeLocal(d.count);
        });

        function initSwAndPush() {
            navigator.serviceWorker
                .register(serviceWorkerUrl, { scope: "/" })
                .then(function (registration) {
                    swRegistration = registration;
                    checkAndPromptPush();
                    scheduleAppBadgeSync();
                })
                .catch(function () {});
        }

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", initSwAndPush);
        } else {
            initSwAndPush();
        }

        window.addEventListener("pageshow", function (event) {
            if (event.persisted && swRegistration) {
                checkAndPromptPush();
            }
            scheduleAppBadgeSync();
        });

        document.addEventListener("visibilitychange", function () {
            if (!document.hidden) {
                scheduleAppBadgeSync();
            }
        });

        window.addEventListener("focus", scheduleAppBadgeSync);
    }
})();
</script>
