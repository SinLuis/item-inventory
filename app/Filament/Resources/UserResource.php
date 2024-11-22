<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    TextInput::make('name')->label(trans('Name'))->required(),
                    TextInput::make('email')->label(trans('Email'))->email()->required()->unique(ignorable: fn($record) => $record),
                    TextInput::make('password')->label(trans('Password'))->password()->dehydrateStateUsing(fn ($state) => Hash::make($state))->required(fn (Page $livewire): bool => $livewire instanceof CreateRecord)->minlength('8')->same('passwordConfirmation'),
                    TextInput::make('passwordConfirmation')->label(trans('Password Confirmation'))->password()->dehydrateStateUsing(fn ($state) => Hash::make($state))->required(fn (Page $livewire): bool => $livewire instanceof CreateRecord)->minlength('8'),
                    Select::make('roles')->multiple()->relationship('roles', 'name')->preload(),
                    Select::make('permissions')->multiple()->relationship('permissions', 'name')->preload()
                ])
                ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable()->toggleable(),
                TextColumn::make('email')->copyable()->sortable()->searchable()->toggleable(),
                // TextColumn::make('roles.name')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name'),
                TextEntry::make('email'),
                TextEntry::make('roles.name')->placeholder("Doesn't Have Role"),
                TextEntry::make('permissions.name')->placeholder("Doesn't Have Permission")
            ]);
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
