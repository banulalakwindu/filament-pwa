<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Resolvers;

use Banulakwin\FilamentPwa\Contracts\DefinesWebPushRecipientsQuery;
use Banulakwin\FilamentPwa\Contracts\WebPushRecipientResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class ConfigWebPushRecipientResolver implements WebPushRecipientResolver
{
    public function resolve(): Collection
    {
        $class = (string) config('filament-pwa.notifiable_model');
        if (! class_exists($class) || ! is_subclass_of($class, Model::class)) {
            throw new InvalidArgumentException('filament-pwa.notifiable_model must be an Eloquent model class.');
        }

        $strategy = (string) config('filament-pwa.recipient_strategy', 'config');

        if ($strategy === 'query') {
            if (! is_subclass_of($class, DefinesWebPushRecipientsQuery::class)) {
                throw new InvalidArgumentException(
                    'filament-pwa.recipient_strategy=query requires the notifiable model to implement DefinesWebPushRecipientsQuery.',
                );
            }

            /** @var DefinesWebPushRecipientsQuery&Model $class */
            return $class::webPushRecipientQuery()->get();
        }

        /** @var class-string<Model> $class */
        $query = $class::query();
        /** @var array<string, mixed> $where */
        $where = config('filament-pwa.recipient_criteria.where', []);
        if ($where !== []) {
            $query->where($where);
        }

        return $query->get();
    }
}
