<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Contracts;

use Illuminate\Support\Collection;

interface WebPushRecipientResolver
{
    /**
     * @return Collection<int, object>
     */
    public function resolve(): Collection;
}
