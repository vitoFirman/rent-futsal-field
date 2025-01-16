<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'User Management';

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
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->unique(table: User::class, column: 'email', ignoreRecord: true)->required(),
                TextInput::make('password')->password()->required()->revealable()->confirmed()->visible(fn($record) => $record === null),
                TextInput::make('password_confirmation')->password()->required()->revealable()->visible(fn($record) => $record === null),
                TextInput::make('phone_number')->required(),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->options(function () {
                        // Mendapatkan peran yang sedang login
                        $user = Auth::user();

                        // Jika pengguna adalah 'superadmin', tampilkan semua peran
                        if ($user->roles->contains('name', 'super_admin')) {
                            return DB::table('roles')->pluck('name', 'id');
                        }

                        // Jika pengguna adalah 'admin', tampilkan hanya 'admin' dan 'customer'
                        if ($user->roles->contains('name', 'admin')) {
                            return DB::table('roles')->whereIn('name', ['admin', 'customer'])->pluck('name', 'id');
                        }

                        // Jika pengguna lainnya, tampilkan hanya 'customer'
                        return DB::table('roles')->where('name', 'customer')->pluck('name', 'id');
                    })
                    ->preload()
                    ->searchable(),
                TextInput::make('address')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->searchable(),
                TextColumn::make('email'),
                TextColumn::make('phone_number'),
                TextColumn::make('roles.name')->sortable(),
                TextColumn::make('address'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->visible(function ($record) {
                    $user = Auth::user(); // Ambil user yang sedang login
                    $role = $user->roles->first()->name ?? ''; // Ambil peran user yang sedang login

                    if ($role === 'admin' && $record->roles->first()->name !== 'customer') {
                        return false;
                    }

                    if ($role === 'superadmin') {
                        return true;
                    }

                    return true;
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
