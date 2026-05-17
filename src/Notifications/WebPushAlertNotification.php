<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Notifications;

use Banulakwin\FilamentPwa\Contracts\BadgeCountProvider;
use Banulakwin\FilamentPwa\Notifications\Channels\MinishlinkWebPushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

final class WebPushAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public string $url,
        public string $type = 'admin-flow',
        public string $priority = 'normal',
        public string $icon = '/favicon/web-app-manifest-192x192.png',
        public ?int $badgeCount = null,
    ) {}

    /** @return array<int, class-string> */
    public function via(mixed $notifiable): array
    {
        return [MinishlinkWebPushChannel::class];
    }

    /**
     * @return array{
     *     title: string,
     *     body: string,
     *     icon: string,
     *     badgeCount: int,
     *     data: array{url: string, type: string, priority: string}
     * }
     */
    public function toWebPush(mixed $notifiable): array
    {
        $badge = $this->badgeCount;
        if ($badge === null) {
            $badge = app(BadgeCountProvider::class)->count();
        }

        return [
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'badgeCount' => $badge,
            'data' => [
                'url' => $this->url,
                'type' => $this->type,
                'priority' => $this->priority,
            ],
        ];
    }
}
