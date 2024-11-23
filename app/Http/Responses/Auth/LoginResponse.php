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

        if ($user->hasRole('admin')) {
            Filament::setCurrentPanel($panels['admin']);
        } else if ($user->hasRole('karyawan')) {
            Filament::setCurrentPanel($panels['karyawan']);
        } else if ($user->hasRole('personalia')) {
            Filament::setCurrentPanel($panels['personalia']);
        } else {
            Filament::setCurrentPanel($panels['admin']);
        }

        return parent::toResponse($request);
    }
}
