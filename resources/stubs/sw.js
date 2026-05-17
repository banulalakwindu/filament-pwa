async function applyBadgeInServiceWorker(n) {
    try {
        if (n > 0) {
            if (typeof navigator.setAppBadge === 'function') {
                await navigator.setAppBadge(n);
            }
        } else if (typeof navigator.clearAppBadge === 'function') {
            await navigator.clearAppBadge();
        } else if (typeof navigator.setAppBadge === 'function') {
            await navigator.setAppBadge(0);
        }
    } catch {
        // unsupported or blocked
    }
}

async function broadcastBadgeToWindowClients(n) {
    try {
        const list = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
        for (const client of list) {
            client.postMessage({ type: 'FILAMENT_PWA_APP_BADGE', count: n });
        }
    } catch {
        // ignore
    }
}

function parseBadgeCount(payload) {
    if (!payload || !Object.prototype.hasOwnProperty.call(payload, 'badgeCount')) {
        return null;
    }

    const n = Number(payload.badgeCount);
    if (!Number.isFinite(n) || n < 0) {
        return null;
    }

    return n;
}

async function applyBadgeFromPayload(payload) {
    const n = parseBadgeCount(payload);
    if (n === null) {
        return;
    }

    await applyBadgeInServiceWorker(n);
    await broadcastBadgeToWindowClients(n);
}

function parsePriority(payload) {
    const rawPriority = payload?.data?.priority ?? payload?.priority ?? 'normal';
    const normalized = String(rawPriority).toLowerCase();

    if (normalized === 'high' || normalized === 'low') {
        return normalized;
    }

    return 'normal';
}

function defaultOpenPath() {
    return '/admin';
}

function buildNotificationOptions({ body, url, badgeN, priority, type }) {
    const isHighPriority = priority === 'high';
    const isLowPriority = priority === 'low';
    const options = {
        body,
        icon: isHighPriority ? '/favicon/web-app-manifest-512x512.png' : '/favicon/favicon-96x96.png',
        badge: '/favicon/web-app-manifest-192x192.png',
        data: {
            url,
            priority,
            type,
            ...(badgeN !== null ? { badgeCount: badgeN } : {}),
        },
    };

    if (isHighPriority) {
        options.requireInteraction = true;
        options.renotify = true;
        options.vibrate = [200, 100, 200, 100, 300];
        options.tag = `filament-pwa-high-${type || 'alert'}`;
    } else if (isLowPriority) {
        options.silent = true;
    }

    return options;
}

self.addEventListener('push', (event) => {
    let title = 'Notification';
    let body = '';
    let url = defaultOpenPath();
    let payload = null;

    try {
        if (event.data) {
            payload = event.data.json();
            title = payload.title ?? title;
            body = payload.body ?? body;
            url = payload.url ?? payload.data?.url ?? url;
        }
    } catch {
        if (event.data) {
            body = event.data.text();
        }
    }

    const badgeN = parseBadgeCount(payload);
    const priority = parsePriority(payload);
    const type = payload?.data?.type ?? payload?.type ?? 'admin-flow';

    event.waitUntil(
        (async () => {
            if (badgeN !== null) {
                await applyBadgeFromPayload(payload);
            }

            const options = buildNotificationOptions({
                body,
                url,
                badgeN,
                priority,
                type,
            });

            await self.registration.showNotification(title, options);
        })(),
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const data = event.notification.data || {};
    const url = data.url ?? defaultOpenPath();
    const badgeRaw = data.badgeCount;

    event.waitUntil(
        (async () => {
            if (badgeRaw !== undefined && badgeRaw !== null) {
                const n = Number(badgeRaw);
                if (Number.isFinite(n) && n >= 0) {
                    await applyBadgeInServiceWorker(n);
                    await broadcastBadgeToWindowClients(n);
                }
            }

            await self.clients.openWindow(url);
        })(),
    );
});
