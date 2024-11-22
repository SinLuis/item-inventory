<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UofmResource\Pages;
use App\Filament\Resources\UofmResource\RelationManagers;
use App\Models\Uofm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UofmResource extends Resource
{
    protected static ?string $model = Uofm::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Master';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    TextInput::make('code')->label(trans('Uofm ID'))->required(),
                    TextInput::make('description')->label(trans('Uofm Description'))->required()
                    // Select::make('class_id')->options(ClassItem::all()->pluck('class_item_id'))->required(),
                    // Select::make('uofm')->options(Uofm::all()->pluck('uofm_id'))->required()
                ])
                ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->sortable()->searchable()->toggleable(),
                TextColumn::make('description')->copyable()->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUofms::route('/'),
            'create' => Pages\CreateUofm::route('/create'),
            'edit' => Pages\EditUofm::route('/{record}/edit'),
        ];
    }
}
