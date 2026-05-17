<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class PushSubscription extends Model
{
    protected $table = 'push_subscriptions';

    /** @var list<string> */
    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
    ];

    /** @return MorphTo<Model, $this> */
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }
}
