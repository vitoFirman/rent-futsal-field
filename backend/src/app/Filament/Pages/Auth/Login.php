<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $view = 'filament-panels::pages.auth.login';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])->statePath('data');
    }

    protected function throwFailureValidationException(): never
    {
        \Filament\Notifications\Notification::make()
            ->title(__('Login Failed'))
            ->body(__('Invalid Email Or Password'))
            ->danger()
            ->duration(5000)
            ->send();

        throw ValidationException::withMessages([
            'data.email' => ' ',
            'data.password' => ' ',
        ]);
    }
}
