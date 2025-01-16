<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FieldResource\Pages;
use App\Filament\Resources\FieldResource\RelationManagers;
use App\Models\Field;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as ComponentsGrid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

use function Livewire\wrap;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'System Management';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Grid::make(3)
                    ->schema([
                        TextInput::make('name')->required(),
                        Select::make('type')
                            ->options([
                                'Vinyil' => 'Vinyil',
                                'Rumput sintetis' => 'Rumput sintetis',
                                'Semen' => 'Semen',
                                'Parquette' => 'Parquette',
                                'Taraflex' => 'Taraflex',
                                'Karpet Plastik' => 'Karpet Plastik',
                            ])
                            ->native(false)
                            ->required(),
                        TextInput::make('price_per_hour')
                            ->prefix('Rp ')
                            ->label('Price per hour')
                            ->required()
                            ->numeric(),
                    ]),
                Textarea::make('description')->columnSpanFull()->rows(6),
                FileUpload::make('thumbnail')->image()->columnSpanFull(),
                Section::make('image for field')
                    ->schema([
                        Repeater::make('field_image')->label('')
                            ->schema([
                                FileUpload::make('image')->image()
                            ])
                            ->relationship('fieldImages')
                            ->columnSpanFull()
                    ])
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsGrid::make(1)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('type'),
                        TextEntry::make('price_per_hour')
                            ->getStateUsing(fn(Field $record) => number_format($record->price_per_hour, 0, '.', '.'))
                            ->prefix('Rp ')
                            ->badge()
                            ->color('success'),
                    ]),
                TextEntry::make('description')->formatStateUsing(fn(string $state): HtmlString => new HtmlString($state)),
                ComponentsGrid::make(1)
                    ->schema([
                        ImageEntry::make('thumbnail')->simpleLightbox(fn($record) =>  $record?->image),
                        ImageEntry::make('fieldImages.image')->simpleLightbox(fn($record) =>  $record?->image)
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')->searchable(),
                TextColumn::make('price_per_hour')->prefix('Rp ')->label('Price/hour')->getStateUsing(fn(Field $record) => number_format($record->price_per_hour, 0, '.', '.')),
                TextColumn::make('description')->wrap()->extraAttributes(['style' => 'width: 300px;']),
                ImageColumn::make('thumbnail'),
                ImageColumn::make('fieldImages.image')->stacked()->limit(4)->circular()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListFields::route('/'),
            'create' => Pages\CreateField::route('/create'),
            'view' => Pages\ViewField::route('/{record}'),
            'edit' => Pages\EditField::route('/{record}/edit'),
        ];
    }
}
