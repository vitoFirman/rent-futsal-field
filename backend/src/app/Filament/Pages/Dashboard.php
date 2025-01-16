<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;


class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        $user = Auth::user();
        if ($user->roles->pluck('name')[0] !== 'admin' && $user->roles->pluck('name')[0] !== 'super_admin') {
            return $form->schema([]);
        }

        $years = [];
        $currentYear = now()->year;
        $startYear = now()->subYears(10)->year;

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $years[$year] = (string)$year;
        }

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('month')
                            ->options([
                                1 => 'January',
                                2 => 'February',
                                3 => 'March',
                                4 => 'April',
                                5 => 'May',
                                6 => 'June',
                                7 => 'July',
                                8 => 'August',
                                9 => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December',
                            ])
                            ->native(false)
                            ->default(now()->month)
                            ->selectablePlaceholder(false)
                            ->live(),
                        Select::make('year')
                            ->native(false)
                            ->options($years)
                            ->default($currentYear)
                            ->live(),
                    ])
                    ->columns(2),
            ]);
    }
}
