<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Forms\Components\Grid;
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
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class CustomProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 15;

    public Model $user;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->form->fill($this->user->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profile Information')
                    ->aside(false)
                    ->description('Update your account profile information')
                    ->schema([
                        //
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')->required(),
                                TextInput::make('email')->required(),
                                TextInput::make('phone_number')->required(),
                                TextInput::make('address')->required(),
                            ])
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->user->update($data);

        Notification::make()
            ->success()
            ->title('User data updated successfully')
            ->send();
    }

    public function render(): View
    {
        return view('livewire.custom-profile-component');
    }
}
