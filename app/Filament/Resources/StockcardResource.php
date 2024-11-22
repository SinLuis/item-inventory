<?php

namespace App\Filament\Resources;

use App\Exports\StockcardExport;
use App\Filament\Resources\StockcardResource\Pages;
use App\Filament\Resources\StockcardResource\RelationManagers;
use App\Models\Stockcard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class StockcardResource extends Resource
{
    protected static ?string $model = Stockcard::class;

    protected static ?string $navigationIcon = 'heroicon-o-stop';
    protected static ?string $navigationGroup = 'Storages';
    protected static ?int $navigationSort = 3;

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
                TextColumn::make('pib_number')->searchable()->toggleable(),
                TextColumn::make('seri_number')->searchable()->toggleable(),
                TextColumn::make('document_date')->searchable()->toggleable(),
                TextColumn::make('transaction_description')->searchable()->toggleable(),
                TextColumn::make('item.class.code')->searchable()->toggleable(),
                TextColumn::make('item.code')->label('Item Code')->searchable()->toggleable(),
                TextColumn::make('item.description')->label('Item Description')->searchable()->toggleable(),
                TextColumn::make('quantity_in')->label('Quantity In')->getStateUsing(function ($record) {
                        return $record->transaction_type == 1 
                            ? $record->total_quantity 
                            : 0;
                    })->toggleable(),
                TextColumn::make('quantity_out')->label('Quantity Out')->getStateUsing(function ($record) {
                        return $record->transaction_type == 2 
                            ? $record->total_quantity 
                            : 0;
                    })->toggleable(),
                TextColumn::make('item.uofm.code')->searchable()->toggleable(),
                TextColumn::make('storages.storage')->searchable()->toggleable(),
            ])
            ->filters([
                Filter::make('document_date_range')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()->closeonDateSelection(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()->closeonDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['start_date']) && isset($data['end_date'])) {
                            $query->whereBetween('document_date', [
                                $data['start_date'],
                                $data['end_date'],
                            ]);
                        }
                    }),
                    
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('export')
                        ->label('Export to Excel')
                        ->action(function ($records) {
                            $recordIds = $records->pluck('id')->toArray(); // Extract only the IDs
                            return Excel::download(new StockcardExport($recordIds), 'Stock Card.xlsx');
                        })
                        ->requiresConfirmation(),
                ])->label('Export'),
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
            'index' => Pages\ListStockcards::route('/'),
            'create' => Pages\CreateStockcard::route('/create'),
            'edit' => Pages\EditStockcard::route('/{record}/edit'),
        ];
    }
}
