<?php

namespace App\Http\Responses\Auth;

use App\Providers\Filament\AdminPanelProvider;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\LoginResponse as AuthLoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends AuthLoginResponse
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = Filament::auth()->user();
        $panels = Filament::getPanels();

        $role = $user->getRoleNames()[0];
        Filament::setCurrentPanel($panels[$role]);

        return parent::toResponse($request);
    }
}
