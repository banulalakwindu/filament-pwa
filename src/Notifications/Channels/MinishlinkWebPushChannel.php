<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Notifications\Channels;

use Banulakwin\FilamentPwa\Models\PushSubscription;
use Illuminate\Notifications\Notification;
use JsonException;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

final class MinishlinkWebPushChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWebPush')) {
            return;
        }

        $subject = config('filament-pwa.vapid.subject');
        $publicKey = config('filament-pwa.vapid.public_key');
        $privateKey = config('filament-pwa.vapid.private_key');

        if (
            ! is_string($subject) || mb_trim($subject) === ''
            || ! is_string($publicKey) || $publicKey === ''
            || ! is_string($privateKey) || $privateKey === ''
        ) {
            return;
        }

        try {
            /** @var array<string, mixed> $payload */
            $payload = $notification->toWebPush($notifiable);
        } catch (Throwable) {
            return;
        }

        if (! is_array($payload) || $payload === []) {
            return;
        }

        try {
            $json = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        } catch (JsonException) {
            return;
        }

        if (! method_exists($notifiable, 'pushSubscriptions')) {
            return;
        }

        $subscriptions = $notifiable->pushSubscriptions()->get();
        if ($subscriptions->isEmpty()) {
            return;
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => mb_trim($subject),
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ]);
        } catch (Throwable) {
            return;
        }

        foreach ($subscriptions as $row) {
            if ((string) $row->public_key === '' || (string) $row->auth_token === '') {
                continue;
            }

            try {
                $subscription = Subscription::create([
                    'endpoint' => $row->endpoint,
                    'keys' => [
                        'p256dh' => $row->public_key,
                        'auth' => $row->auth_token,
                    ],
                    'contentEncoding' => $row->content_encoding ?? 'aesgcm',
                ]);

                $webPush->queueNotification($subscription, $json);
            } catch (Throwable) {
                continue;
            }
        }

        try {
            foreach ($webPush->flush() as $report) {
                if ($report->isSubscriptionExpired()) {
                    PushSubscription::query()->where('endpoint', $report->getEndpoint())->delete();
                }
            }
        } catch (Throwable) {
            // Keep subscriptions for retries on transient network failures.
        }
    }
}
