<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Role names accepted in route definitions, e.g. "role:admin,editor".
     *
     * @var array<string, int>
     */
    private const ROLES = [
        'admin' => Role::ADMIN,
        'editor' => Role::EDITOR,
        'user' => Role::USER,
    ];

    /**
     * Let the request through only when the user has one of the given roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowed = array_map(fn (string $role) => self::ROLES[$role], $roles);

        abort_unless($request->user()?->hasRole(...$allowed), Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
