<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Concerns;

use Banulakwin\FilamentPwa\Models\PushSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasWebPushSubscriptions
{
    /** @return MorphMany<PushSubscription, Model> */
    public function pushSubscriptions(): MorphMany
    {
        return $this->morphMany(PushSubscription::class, 'subscribable');
    }

    public function updatePushSubscription(
        string $endpoint,
        string $key,
        string $token,
        ?string $contentEncoding = 'aesgcm',
    ): void {
        $this->pushSubscriptions()->updateOrCreate(
            ['endpoint' => $endpoint],
            [
                'public_key' => $key,
                'auth_token' => $token,
                'content_encoding' => $contentEncoding,
            ],
        );
    }
}
