<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'System Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Grid::make(3)
                    ->schema([
                        TextInput::make('booking_id')->readOnly(),
                        TextInput::make('amount')->readOnly(),
                        DatePicker::make('payment_date'),
                        Select::make('payment_method')
                            ->options([
                                'cash' => 'cash',
                                'transfer' => 'transfer',
                            ])->native(false),
                        Select::make('status')
                            ->options([
                                'paid' => 'paid',
                                'unpaid' => 'unpaid',
                            ])->native(false),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('booking.id')->label('Booking id'),
                TextColumn::make('booking.user.name'),
                TextColumn::make('amount')->prefix('Rp ')->getStateUsing(fn(Payment $record) => number_format($record->amount, 0, '.', '.')),
                TextColumn::make('payment_date')->date('d-m-Y'),
                TextColumn::make('payment_method'),
                TextColumn::make('status')
                    ->icon(fn($state) => $state == 'paid' ? 'heroicon-o-check' : 'heroicon-o-x-mark')
                    ->badge()
                    ->colors([
                        'danger' => fn($state): bool => $state == 'unpaid',
                        'success' => fn($state): bool => $state == 'paid'
                    ]),
            ])
            ->filters([
                //
                Filter::make('payment_date_today')
                    ->label('Payment Date Today')
                    ->query(fn(Builder $query) => $query->whereDate('payment_date', '=', now()->toDateString())),
                SelectFilter::make('status')
                    ->options([
                        'paid' => 'paid',
                        'unpaid' => 'unpaid',
                    ]),
                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'cash',
                        'transfer' => 'transfer',
                    ]),
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
            'index' => Pages\ListPayments::route('/'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
