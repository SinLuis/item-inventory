<?php

namespace App\Filament\Resources;

use App\Exports\WasteExport;
use App\Filament\Resources\WasteResource\Pages;
use App\Filament\Resources\WasteResource\RelationManagers;
use App\Models\Waste;
use App\Models\Bbout;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkAction;
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

class WasteResource extends Resource
{
    protected static ?string $model = Waste::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('document_number')->label(trans('Nomor BC24'))->required(),
                DatePicker::make('document_date')->label(trans('Tanggal BC24'))->native(false)->closeonDateSelection()->required(),
                
                Select::make('bbout_id') 
                        ->label(trans('Daftar WIP'))
                        ->options(function () {
                            $bbout = Bbout::query()->get();

                            // Log the retrieved BBins to see what's being fetched
                            Log::info('BBOut Retrieved:', $bbout->toArray());
        
                            // Check if the collection is empty
                            if ($bbout->isEmpty()) {
                                return []; // Return an empty array if no BBins are found
                            }
        
                            // Map through the BBins and return the desired format
                            return $bbout->mapWithKeys(function ($bbout) {
                                // Check if the bbin is valid and has remaining quantity
                                if ($bbout && $bbout->quantity_remaining > 0) {
                                    return [
                                        $bbout->id => $bbout->bbin->document->code . ': ' . $bbout->pib_number . 
                                                    ', No Seri: ' . $bbout->seri_number . 
                                                    ', ' . $bbout->item_id .
                                                    ' ' . $bbout->item_description . 
                                                    ', Waste: ' . $bbout->quantity_remaining . 
                                                    ' ' . $bbout->item_uofm 
                                                    // ' - Gudang: ' . $bbout->storage->storage
                                    ]; 
                                }
        
                                return []; // Return an empty array for invalid bbins
                            })->toArray();
                        })
                        ->searchable()
                        ->reactive()->afterStateUpdated(function (callable $set, $state) {
                            $bbout = Bbout::find($state); 
                           
                            if ($bbout) {
                                $set('pib_number', $bbout->pib_number);
                                $set('seri_number', $bbout->seri_number);
                                // $set('item_id', $hpin->item_id);
                                // $set('item_description', $hpin->item_description);
                                // $set('item_uofm', $hpin->item_uofm);
                                $set('total_quantity', $bbout->quantity_remaining);

                            } else {
                                $set('pib_number', null);
                                $set('seri_number', null);
                                // $set('item_id', null);
                                // $set('item_description', null);
                                // $set('item_uofm', null);
                                $set('total_quantity', null);
                            }
                            
                        }),
                        
                        TextInput::make('pib_number')->label(trans('PIB'))->readOnly()->required(),
                        TextInput::make('seri_number')->label(trans('No Seri'))->readOnly(),
                        
                        Select::make('item_code')->relationship('item', 'code', function(Builder $query){
                            return $query->where('class_id', 3);
                        })->label(trans('Kode Barang'))->required()->searchable()->preload()->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $item = Item::find($state);
                            if ($item) {
                                $set('item_id', $item->id);
                                $set('item_code', $item->code);
                                $set('item_description', $item->description);
                                $set('item_longdescription', $item->long_description);
                                $set('item_uofm', $item->uofm->code);
                            } else {
                                $set('item_id', null);
                                $set('item_code', null);
                                $set('item_description', null);
                                $set('item_longdescription', null);
                                $set('item_uofm', null);
                            }
                        }),

                        // TextInput::make('item_id')->label(trans('Kode Barang')),
                        Hidden::make('item_id'),
                        Hidden::make('item_code'),
                        TextInput::make('item_description')->label(trans('Nama Barang'))->readOnly(),
                        TextInput::make('item_uofm')->label(trans('Satuan'))->readOnly(),
                        TextInput::make('total_quantity')->label('Jumlah')->numeric()->required()->rule('numeric')->rule('gt:0'),
                        TextInput::make('item_amount')->label('Nilai Barang')->numeric()->required()->rule('numeric')->rule('min:0'),
                        Hidden::make('user_id')->default(auth()->id()),
                        TextInput::make('user_name')
                        ->label(trans('User'))
                        ->default(auth()->user()->name) // Mengatur nilai default menjadi nama pengguna saat ini
                        ->readOnly(),
            ]);
    }

    public static function getLabel(): string
    {
        return 'Waste';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pib_number')->label('PIB')->sortable()->searchable()->toggleable(),
                TextColumn::make('seri_number')->label('Seri Number')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_number')->label('Nomor BC24')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_date')->label('Tanggal BC24')->sortable()->searchable()->toggleable(),
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
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('export')
                        ->label('Export to Excel')
                        ->action(function ($records) {
                            $recordIds = $records->pluck('id')->toArray(); // Extract only the IDs
                            return Excel::download(new WasteExport($recordIds), 'Waste.xlsx');
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
            'index' => Pages\ListWastes::route('/'),
            'create' => Pages\CreateWaste::route('/create'),
            'edit' => Pages\EditWaste::route('/{record}/edit'),
        ];
    }
}
