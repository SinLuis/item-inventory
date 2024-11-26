<?php

namespace App\Filament\Resources;

use App\Exports\HpinExport;
use App\Filament\Resources\HpinResource\Pages;
use App\Filament\Resources\HpinResource\RelationManagers;
use App\Models\Hpin;
use App\Models\Bbout;
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

class HpinResource extends Resource
{
    protected static ?string $model = Hpin::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-down';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('document_number')->label(trans('Nomor Invoice'))->required(),
                DatePicker::make('document_date')->label(trans('Tanggal Invoice'))->native(false)->closeonDateSelection()->required(),
                
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
                                        $bbout->id => 'PIB: ' . $bbout->pib_number . 
                                                    ', No Seri: ' . $bbout->seri_number . 
                                                    ', ' . $bbout->item->code .
                                                    ' ' . $bbout->fg_description . 
                                                    ', Jumlah: ' . $bbout->quantity_remaining . 
                                                    ' ' . $bbout->item->uofm->code 
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
                                $set('item_id', $bbout->item->id);
                                $set('item_code', $bbout->item->code);
                                $set('item_description', $bbout->fg_description);
                                $set('item_uofm', $bbout->item_uofm);
                                $set('pib_number', $bbout->pib_number);
                                $set('seri_number', $bbout->seri_number);
                                // $set('produce_quantity', $bbout->use_quantity);
                                $set('sub_quantity', $bbout->sub_quantity);
                                
                            
                            } else {
                                $set('item_id', null);
                                $set('item_code', null);
                                $set('item_description', null);
                                $set('item_uofm', null);
                                $set('pib_number', null);
                                $set('seri_number', null);
                                // $set('produce_quantity', null);
                                $set('sub_quantity', null);
                            }
                            
                        }),
                        Hidden::make('item_id'),
                        TextInput::make('item_code')->label(trans('Kode Barang'))->readOnly(),
                        TextInput::make('item_description')->label(trans('Nama Barang'))->readOnly(),
                        TextInput::make('item_uofm')->label(trans('Satuan'))->readOnly(),
                        TextInput::make('pib_number')->label(trans('No PIB'))->readOnly()->required(),
                        TextInput::make('seri_number')->label(trans('No Seri'))->readOnly(),
                        TextInput::make('produce_quantity')->label(trans('Jumlah dari Produksi'))->numeric()->required()->rule('min:0'),
                        //  Hidden::make('quantity_remaining'),
                        TextInput::make('sub_quantity')->label(trans('Jumlah dari Subkontrak'))->numeric()->rule('min:0'),
                        Select::make('storages_id')->relationship('storage', 'storage')->label(trans('Gudang'))->preload()->required(),
                        Hidden::make('user_id')->default(auth()->id()),
                        TextInput::make('user_name')
                        ->label(trans('User'))
                        ->default(auth()->user()->name) // Mengatur nilai default menjadi nama pengguna saat ini
                        ->readOnly(),
                    ]);
    }

    public static function create(Create $create): void
    {
            // Override create method to set user_id
            $create->afterSave(function ($record) {
                if (!$record->user_id) {
                    $record->user_id = auth()->id();
                    // $record->save();
                }
                $record->user_name = auth()->user()->name; // Atau bisa diambil dari input jika dibutuhkan
                $record->save();
            });
    }

    public static function getLabel(): string
    {
        return 'HP IN';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')->label('Nomor Surat')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_date')->label('Tanggal Surat')->sortable()->searchable()->toggleable(),
                TextColumn::make('item_id')->label('Kode Barang')->sortable()->searchable()->toggleable(),
                TextColumn::make('item_description')->label('Nama Barang')->sortable()->searchable()->toggleable(),
                TextColumn::make('item_uofm')->label('Satuan')->sortable()->searchable()->toggleable(),
                TextColumn::make('produce_quantity')->label('Jumlah dari Produksi')->sortable()->searchable()->toggleable(),
                TextColumn::make('sub_quantity')->label('Jumlah dari Subkontrak')->sortable()->searchable()->toggleable(),
                TextColumn::make('storage.storage')->label('Gudang')->sortable()->searchable()->toggleable(),
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
                            return Excel::download(new HpinExport($recordIds), 'Hasil Produksi Masuk.xlsx');
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
            'index' => Pages\ListHpins::route('/'),
            'create' => Pages\CreateHpin::route('/create'),
            'edit' => Pages\EditHpin::route('/{record}/edit'),
        ];
    }
}
