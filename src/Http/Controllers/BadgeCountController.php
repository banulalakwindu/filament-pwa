<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Http\Controllers;

use Banulakwin\FilamentPwa\Contracts\BadgeCountProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class BadgeCountController extends Controller
{
    public function __construct(
        private readonly BadgeCountProvider $badgeCountProvider,
    ) {}

    public function show(): JsonResponse
    {
        return response()->json([
            'count' => $this->badgeCountProvider->count(),
        ]);
    }
}
