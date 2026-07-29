<?php

namespace App\Http\Middleware;

use App\Models\CarWash;
use App\Support\CurrentCarWash;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCarWashPermissionContext
{
    public function __construct(
        private readonly CurrentCarWash $currentCarWash,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $routeValue = $request->route('carWash');

        $carWash = $routeValue instanceof CarWash
            ? $routeValue
            : CarWash::query()->where('slug', $routeValue)->firstOrFail();

        $request->route()?->setParameter('carWash', $carWash);

        $previousTeamId = getPermissionsTeamId();

        setPermissionsTeamId($carWash->getKey());
        $this->currentCarWash->set($carWash);
        $request->user()?->unsetRelation('roles')->unsetRelation('permissions');

        try {
            return $next($request);
        } finally {
            $request->user()?->unsetRelation('roles')->unsetRelation('permissions');
            setPermissionsTeamId($previousTeamId);
            $this->currentCarWash->clear();
        }
    }
}
