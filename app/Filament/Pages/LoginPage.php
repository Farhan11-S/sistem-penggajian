<?php

namespace App\Filament\Pages;

use App\Http\Responses\Auth\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Login;

class LoginPage extends Login
{
    public function mount(): void
    {
        if (Filament::auth()->check()) {
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

            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
