<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use App\Models\ClassItem;
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

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationGroup = 'Master';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    TextInput::make('code')->label(trans('Item ID'))->required(),
                    TextInput::make('description')->label(trans('Item Description'))->required(),
                    TextInput::make('long_description')->label(trans('Long Description'))->required(),
                    Select::make('class_id')->relationship('class', 'description')->required(),
                    Select::make('uofm_id')->relationship('uofm', 'code')->required(),
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
                TextColumn::make('description')->sortable()->searchable()->toggleable(),
                TextColumn::make('class.description')->sortable()->searchable()->toggleable(),
                TextColumn::make('uofm.code')->sortable()->searchable()->toggleable()
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }

    public static function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with('classItem');
    }

}
