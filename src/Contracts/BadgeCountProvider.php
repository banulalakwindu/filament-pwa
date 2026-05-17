<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Contracts;

interface BadgeCountProvider
{
    public function count(): int;
}
