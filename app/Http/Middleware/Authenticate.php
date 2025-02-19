<?php

namespace App\Http\Middleware;

use App\Models\StatusGajiKaryawan;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Database\Eloquent\Model;

class Authenticate extends Middleware
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        if ($request->getPathInfo() == "/logout") return;

        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();

        $panel = Filament::getCurrentPanel();

        // abort_if(
        //     !$user->canAccessPanel($panel),
        //     403,
        // );

        if (!$user->canAccessPanel($panel)) {
            $supposedPanel = Filament::getPanel($user->getRoleNames()[0]);
            Filament::setCurrentPanel($supposedPanel);
        }
    }

    protected function redirectTo($request): ?string
    {
        // return Filament::getLoginUrl();

        return '/login';
    }
}
