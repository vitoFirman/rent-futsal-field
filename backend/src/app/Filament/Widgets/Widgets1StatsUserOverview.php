<?php

namespace App\Filament\Widgets;

use App\Models\Field;
use App\Models\Payment;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class Widgets1StatsUserOverview extends BaseWidget
{
    use HasWidgetShield;
    use InteractsWithPageFilters;
    protected function getStats(): array
    {
        $month = $this->filters['month'];
        $year = $this->filters['year'];

        $monthName = Carbon::createFromFormat('m', $month)->format('F');

        $incomePaid = Payment::query()
            ->where('status', 'paid')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount');

        $incomeUnpaid = Payment::query()
            ->where('status', 'unpaid')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount');
        $totalIncome = Payment::query()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount');

        return [
            Stat::make('Income Paid', 'Rp ' . number_format($incomePaid, 0, '.', '.'))
                ->description('Income paid ' . $monthName .  ' ' . $year)
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
            Stat::make('Income Unpaid', 'Rp ' . number_format($incomeUnpaid, 0, '.', '.'))
                ->description('Income unpaid ' . $monthName .  ' ' . $year)
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('danger'),
            Stat::make('Total Income', 'Rp ' . number_format($totalIncome, 0, '.', '.'))
                ->description('Total Income ' . $monthName .  ' ' . $year)
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}
