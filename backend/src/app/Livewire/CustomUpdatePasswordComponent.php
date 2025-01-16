<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class CustomUpdatePasswordComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 16;

    public User $user;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->form->fill($this->user->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Update Password')
                    ->description('Ensure your account is using long, random password to stay secure.')
                    ->collapsed()
                    ->schema([
                        TextInput::make('current_password')->password()->rules('current_password')->required()->revealable(),
                        TextInput::make('password')->password()->required()->revealable()->minLength(8),
                        TextInput::make('password_confirmation')->password()->required()->revealable()->same('password')->minLength(8)
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->user->update([
            'password' => Hash::make($data['password'])
        ]);

        if (request()->session()->has('password_hash_web')) {

            request()->session()->forget('password_hash_web');
            Auth::guard('web')->login($this->user);
        }

        Notification::make()
            ->success()
            ->title('Password updated successfully')
            ->send();

        $this->form->fill([]);
    }

    public function render(): View
    {
        return view('livewire.custom-update-password-component');
    }
}
