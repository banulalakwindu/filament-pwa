<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface DefinesWebPushRecipientsQuery
{
    public static function webPushRecipientQuery(): Builder;
}
