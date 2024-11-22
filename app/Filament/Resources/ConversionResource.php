<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversionResource\Pages;
use App\Filament\Resources\ConversionResource\RelationManagers;
use App\Models\Conversion;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConversionResource extends Resource
{
    protected static ?string $model = Conversion::class;

    protected static ?string $navigationIcon = 'heroicon-o-funnel';
    protected static ?string $navigationGroup = 'Master';
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    Select::make('fg_id')->relationship('fgitem', 'code')->label('Item Barang Jadi')->required()->reactive()->afterStateUpdated(function ($state, callable $set){
                        if($state) {
                            $item = Item::with('uofm')->find($state);
                            $set('fg_description', $item->description);
                            $set('fg_uofm', $item->uofm ? $item->uofm->code : null);
                        } else{
                            $set('fg_description', null);
                            $set('fg_uofm', null);
                        }

                    }),
                    TextInput::make('fg_description')->label(trans('Description'))->disabled(),
                    TextInput::make('fg_uofm')->label(trans('Uofm'))->disabled(),
                    

                    Select::make('rm_id')->relationship('rmitem', 'code')->label('Item Bahan Baku')->required()->reactive()->afterStateUpdated(function ($state, callable $set){
                        if($state) {
                            $item = Item::with('uofm')->find($state);
                            $set('rm_description', $item->description);
                            $set('rm_uofm', $item->uofm ? $item->uofm->code : null);
                        } else{
                            $set('rm_description', null);
                            $set('rm_uofm', null);
                        }

                    }),
                    TextInput::make('rm_description')->label(trans('Description'))->disabled(),
                    TextInput::make('rm_uofm')->label(trans('Uofm'))->disabled(),
                    TextInput::make('coefficient')->label(trans('Coefficient'))->required()->numeric(),
                    TextInput::make('waste')->label(trans('Waste'))->required()->numeric(),
                ])
                ->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fgitem.code')->sortable()->searchable()->toggleable()->label('Item Barang Jadi'),
                TextColumn::make('fgitem.description')->sortable()->searchable()->toggleable()->label('Description'),
                TextColumn::make('fgitem.uofm.code')->sortable()->searchable()->toggleable()->label('Uofm'),
                TextColumn::make('rmitem.code')->sortable()->searchable()->toggleable()->label('Item Bahan Baku'),
                TextColumn::make('rmitem.description')->sortable()->searchable()->toggleable()->label('Description Bahan Baku'),
                TextColumn::make('rmitem.uofm.code')->sortable()->searchable()->toggleable()->label('Uofm Bahan Baku'),
                TextColumn::make('coefficient')->sortable()->searchable()->toggleable()->label('Coefficient'),
                TextColumn::make('waste')->sortable()->searchable()->toggleable()->label('Waste')
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
            'index' => Pages\ListConversions::route('/'),
            'create' => Pages\CreateConversion::route('/create'),
            'edit' => Pages\EditConversion::route('/{record}/edit'),
        ];
    }
}
