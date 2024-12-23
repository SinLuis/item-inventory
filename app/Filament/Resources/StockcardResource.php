<?php

namespace App\Filament\Resources;

use NumberFormatter;
use App\Exports\StockcardExport;
use App\Filament\Resources\StockcardResource\Pages;
use App\Filament\Resources\StockcardResource\RelationManagers;
use App\Models\Stockcard;
use App\Models\Item;
use App\Models\Storage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
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
                TextColumn::make('item.class.code')->label('Class')->searchable()->toggleable(),
                TextColumn::make('item.code')->label('Item Code')->searchable()->toggleable(),
                TextColumn::make('item.description')->label('Item Description')->searchable()->toggleable(),
                TextColumn::make('beginning_balance')
                    ->label('Beginning Balance')
                    ->getStateUsing(function ($record) {
                     
                        // Calculate the beginning balance
                        $beginningBalance = Stockcard::where('item_id', $record->item_id)->where('storages_id', $record->storages_id)
                        ->where(function ($query) use ($record) {
                            $query->where('document_date', '<', $record->document_date)
                                ->orWhere(function ($subQuery) use ($record) {
                                    $subQuery->where('document_date', '=', $record->document_date)
                                            ->where('created_at', '<', $record->created_at);
                                });
                            // dd(StockCard::where('created_at', $record->created_at)->get());
                        })->selectRaw("
                            SUM(CASE 
                                WHEN transaction_type = 1 THEN total_quantity 
                                WHEN transaction_type = 2 THEN -total_quantity 
                                ELSE 0 
                            END) as balance
                        ")
                            ->value('balance');
                
                        return $beginningBalance ?? 0; // Default to 0 if no transactions exist
                    })->toggleable()->color('success')->formatStateUsing(function ($state) {
                        $formatter = new NumberFormatter('id_ID', NumberFormatter::DECIMAL);
                        return $formatter->format($state);
                    }),
                TextColumn::make('quantity_in')->label('Quantity In')->getStateUsing(function ($record) {
                        return $record->transaction_type == 1 
                            ? $record->total_quantity 
                            : 0;
                        })->toggleable()->formatStateUsing(function ($state) {
                            $formatter = new NumberFormatter('id_ID', NumberFormatter::DECIMAL);
                            return $formatter->format($state);
                        }),
                TextColumn::make('quantity_out')->label('Quantity Out')->getStateUsing(function ($record) {
                        return $record->transaction_type == 2 
                            ? $record->total_quantity 
                            : 0;
                        })->toggleable()->formatStateUsing(function ($state) {
                            $formatter = new NumberFormatter('id_ID', NumberFormatter::DECIMAL);
                            return $formatter->format($state);
                        }),
                TextColumn::make('ending_balance')
                        ->label('Ending Balance')
                        ->getStateUsing(function ($record) {
                            // Calculate the beginning balance

                        $beginningBalance = Stockcard::where('item_id', $record->item_id)->where('storages_id', $record->storages_id)
                        ->where(function ($query) use ($record) {
                            $query->where('document_date', '<', $record->document_date)
                                ->orWhere(function ($subQuery) use ($record) {
                                    $subQuery->where('document_date', '=', $record->document_date)
                                            ->where('created_at', '<', $record->created_at);
                                });
                        })->selectRaw("
                            SUM(CASE 
                                WHEN transaction_type = 1 THEN total_quantity 
                                WHEN transaction_type = 2 THEN -total_quantity 
                                ELSE 0 
                            END) as balance
                        ")
                            ->value('balance') ?? 0;
                        // Adjust with the current record's transaction
                        $currentQuantity = $record->transaction_type == 1 
                            ? $record->total_quantity 
                            : -$record->total_quantity;
                
                        return $beginningBalance + $currentQuantity;
                        })->toggleable()->color('warning')->formatStateUsing(function ($state) {
                            $formatter = new NumberFormatter('id_ID', NumberFormatter::DECIMAL);
                            return $formatter->format($state);
                        }),      
                TextColumn::make('item.uofm.code')->label('Uofm')->searchable()->toggleable(),
                TextColumn::make('storages.storage')->label('Gudang')->toggleable(),
            ])
            ->filters([
                Filter::make('document_date_range')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->closeonDateSelection(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->closeonDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['start_date']) && isset($data['end_date'])) {
                            $query->whereBetween('document_date', [
                                $data['start_date'],
                                $data['end_date'],
                            ]);
                        }
                    }),
                    Filter::make('item_code')
                    ->form([
                        Select::make('code')
                            ->label('Item Code')->relationship('item', 'code')
                            ->placeholder('Enter Item Code')->required(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        // dd($data['code']);
                        if (!empty($data['code'])) {
                 
                            $query->whereHas('item', function (Builder $itemQuery) use ($data) {
                                $itemQuery->where('id', '=', "{$data['code']}");
                            });
                        }
                        else{
                            $query->whereRaw('1 = 0');
                        }
                        // dd($query);
                    }),
                    Filter::make('storages')
                    ->form([
                        Select::make('storage')
                            ->label('Storage')->relationship('storages', 'storage')
                            ->placeholder('Enter Storage')->default(3)->disablePlaceholderSelection()->required(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['storage'])) {
                 
                            $query->whereHas('storages', function (Builder $itemQuery) use ($data) {
                                $itemQuery->where('id', '=', "{$data['storage']}");
                            });
                        }
                       
                    }),
            ])
            // ->defaultSort('document_date', 'asc')
            
            ->headeractions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->action(function () {
                        // Get the filters applied to the table
                        $filters = request()->input('components', []);
                        $snapshot = data_get($filters, '0.snapshot', null);
                        $data = json_decode($snapshot, true); // Decode the snapshot data
            
                        // Access the specific filter values
                        $startDate = data_get($data, 'data.tableFilters.0.document_date_range.0.start_date');
                        $endDate = data_get($data, 'data.tableFilters.0.document_date_range.0.end_date');
                        $itemId = data_get($data, 'data.tableFilters.0.item_code.0.code');
                        $storageId = data_get($data, 'data.tableFilters.0.storages.0.storage');
            
                        // Fetch other values related to the filters
                        $storageDescription = $storageId ? Storage::where('id', $storageId)->value('storage') : null;
                        $itemClass = null;
                        if ($itemId) {
                            $item = Item::find($itemId);
                            $itemCode = $item->code;
                            $itemDescription = $item->description;
                            $itemClass = $item->class->code;
                        }else{
                            Notification::make()
                            ->title('Error')
                            ->body('Item not found.')
                            ->danger()
                            ->send();

                             return;
                        }
            
                        // Define the date for the export (current time)
                        $userExport = auth()->user()->name; // Current logged-in user's name
                        $dateExport = now()->format('d-M-Y H:i:s');
            
                        // Retrieve the filtered records (if filters are applied)
                        $query = Stockcard::query();
                        if ($startDate && $endDate) {
                            $query->whereBetween('document_date', [$startDate, $endDate]);
                        }
                        if ($itemId) {
                            $query->where('item_id', $itemId);
                        }
                        if ($storageId) {
                            $query->where('storages_id', $storageId);
                        }
                        
                        // Get the IDs of the filtered records
                        $recordIds = $query->pluck('id')->toArray();
            
                        // Export the records to Excel
                        return Excel::download(new StockcardExport($recordIds, $startDate, $endDate, $itemId, $itemCode, $itemDescription, $itemClass, $storageId, $storageDescription, $userExport, $dateExport), 'Stock Card.xlsx');
                    })
                    ->requiresConfirmation() // You can keep confirmation if necessary
            
            ])
            
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // BulkAction::make('export')
                    //     ->label('Export to Excel')
                    //     ->action(function ($records) {
                    //         $filters = request()->input('components', []);

                    //         // dd($filters); // Uncomment this to see the structure in detail

                    //         // Extract snapshot data
                    //         $snapshot = data_get($filters, '0.snapshot', null);
                    //         $data = json_decode($snapshot, true); // Decode the JSON snapshot
                            
                    //         // Access the specific filter values
                    //         $startDate = data_get($data, 'data.tableFilters.0.document_date_range.0.start_date');
                    //         $endDate = data_get($data, 'data.tableFilters.0.document_date_range.0.end_date');
                    //         $itemId = data_get($data, 'data.tableFilters.0.item_code.0.code');
                    //         $storageId = data_get($data, 'data.tableFilters.0.storages.0.storage');
                    //         $storageDescription = null;
                    //         $itemClass = null;
                    //         $userExport = auth()->user()->name; // Current logged-in user's name
                    //         $dateExport = now()->format('d-M-Y H:i:s');

                    //         if (!$startDate || !$endDate) {
                    //             $startDate = null;
                    //             $endDate = null;
                    //         }
                    //         if ($itemId) {
                    //             $item = Item::where('id', $itemId)->first();
                    //             $itemId = Item::where('id', $itemId)->value('id');
                    //             $itemCode = Item::where('id', $itemId)->value('code');
                    //             $itemDescription = Item::where('id', $itemId)->value('description');
                    //             $itemClass = $item->class->code;
                    //         }
                    //         if($storageId){
                    //             $storageDescription = Storage::where('id', $storageId)->value('storage');
                    //         }
                    //         $recordIds = $records->pluck('id')->toArray();
                    //         return Excel::download(new StockcardExport($recordIds, $startDate, $endDate, $itemId, $itemCode, $itemDescription, $itemClass, $storageId, $storageDescription, $userExport, $dateExport), 'Stock Card.xlsx');
                    //     })
                    //     ->requiresConfirmation(),
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
