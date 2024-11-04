<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
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
use Illuminate\Validation\Rule;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'Master';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    TextInput::make('supplier_name')->label(trans('Supplier Name'))->required()->reactive()->afterStateUpdated(fn ($state, callable $set) => $set('classSupplierId', null)),
                    Select::make('class_id')->relationship('class', 'class_supplier_description')->required()
                    ->options(function (callable $get){
                        $supplierName = $get('supplier_name');
                        if($supplierName){
                            $supplier = \App\Models\Supplier::where('supplier_name', $supplierName)->first();
                            if ($supplier) {
                                $usedClassIds = $supplier->class()->pluck('id')->toArray();
                                return \App\Models\ClassSupplier::whereNotIn('id', $usedClassIds)->pluck('class_supplier_description', 'id');
                            }
                        }
                        return \App\Models\ClassSupplier::pluck('class_supplier_description', 'id');
                        })->live(onBlur:true)
                    
                    ,
                    TextInput::make('address')->label(trans('Address'))->required(),
                    TextInput::make('phone')->label(trans('Phone Number'))->numeric()->required(),
                    TextInput::make('email')->label(trans('Email'))->email()->required()->unique(ignorable: fn($record) => $record),
                    TextInput::make('pic')->label(trans('PIC'))->required()
                ])

                ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier_name')->sortable()->searchable()->toggleable(),
                TextColumn::make('class.class_supplier_description')->copyable()->sortable()->searchable()->toggleable(),
                TextColumn::make('address')->copyable()->sortable()->searchable()->toggleable(),
                // TextColumn::make('phone')->sortable()->searchable()->toggleable(),
                // TextColumn::make('email')->copyable()->sortable()->searchable()->toggleable(),
                TextColumn::make('pic')->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
