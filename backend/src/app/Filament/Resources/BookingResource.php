<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use App\Models\Field;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'System Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Grid::make(3)
                    ->schema([
                        Select::make('user_id')
                            ->required()
                            ->relationship('user', 'name')
                            ->native(false),
                        Select::make('field_id')
                            ->required()
                            ->relationship('field', 'name')
                            ->native(false)
                            ->reactive(),
                        DatePicker::make('booking_date')->required(),
                        TimePicker::make('start_time')->prefix('start')->seconds(false)->required()->reactive()->native(false),
                        TimePicker::make('end_time')->prefix('end')->seconds(false)->required()->reactive()->native(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $start = $get('start_time');
                                $end = $state;

                                $startTime = \Carbon\Carbon::parse($start);
                                $endTime = \Carbon\Carbon::parse($end);

                                $minutes = $startTime->diffInMinutes($endTime);

                                $hours = floor($minutes / 60);
                                $remainingMinutes = $minutes % 60;

                                if ($get('field_id')) {
                                    $field = Field::where('id', $get('field_id'))->first();

                                    $totalPrice = $hours * $field->price_per_hour + ($remainingMinutes / 60) * $field->price_per_hour;
                                    $formatedTotal = floor($totalPrice / 1000) * 1000;
                                    $set('total_price', $formatedTotal);
                                }

                                $set('playing', $hours . ' jam ' . $remainingMinutes . ' menit');
                            }),
                        TextInput::make('playing'),
                        TextInput::make('total_price')->label('Total price')->required()->prefix('Rp ')->numeric()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('field.name')->searchable(),
                TextColumn::make('booking_date')->date('d-m-Y'),
                TextColumn::make('start_time')->date('H:i'),
                TextColumn::make('end_time')->date('H:i'),
                TextColumn::make('total_price')->prefix('Rp ')->getStateUsing(fn(Booking $record) => number_format($record->total_price, 0, '.', '.')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
