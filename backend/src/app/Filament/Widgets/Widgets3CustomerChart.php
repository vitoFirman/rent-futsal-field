<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class Widgets3CustomerChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Customer';

    protected static string $color = 'info';

    protected function getData(): array
    {
        $data = Trend::model(Booking::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Daily customer booking',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
