<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubkontrakResource\Pages;
use App\Filament\Resources\SubkontrakResource\RelationManagers;
use App\Models\Subkontrak;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubkontrakResource extends Resource
{
    protected static ?string $model = Subkontrak::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSubkontraks::route('/'),
            'create' => Pages\CreateSubkontrak::route('/create'),
            'edit' => Pages\EditSubkontrak::route('/{record}/edit'),
        ];
    }
}
