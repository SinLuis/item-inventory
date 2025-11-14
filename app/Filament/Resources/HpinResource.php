<?php

namespace App\Filament\Resources;

use NumberFormatter;
use App\Exports\HpinExport;
use App\Filament\Resources\HpinResource\Pages;
use App\Filament\Resources\HpinResource\RelationManagers;
use App\Models\Hpin;
use App\Models\Bbout;
use App\Models\Item;
use App\Models\Wip;
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
use Filament\Forms\Components\Repeater;
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
        if ($form->getOperation() === 'create') {
        return $form
            ->schema([
                Repeater::make('rows')
                        ->label('Data Dokumen')
                        ->cloneable()
                        ->defaultItems(1)
                        ->columns(3)
                        ->schema([  
                            Select::make('form_id')
                            ->label('Jenis Produksi')
                            ->options([
                                1 => 'BBOUT',
                                2 => 'WIP',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                $set('source_id', null);
                            }),

                            TextInput::make('document_number')->label(trans('Nomor Invoice'))->required(),
                            DatePicker::make('document_date')->label(trans('Tanggal Invoice'))->native(false)->closeonDateSelection()->required(),
                            
                            Select::make('source_id')
                                ->label('Sumber Data')
                                ->disablePlaceholderSelection()
                                ->options(function (callable $get) {
                                    $formId = $get('form_id');

                                    if ($formId == 1) {
                                        // BBOUT
                                        return \App\Models\Bbout::query()
                                            ->where('quantity_remaining', '>', 0)
                                            ->get()
                                            ->mapWithKeys(function ($bbout) {
                                                return [
                                                    $bbout->id => $bbout->bbin->document->code . ': ' . $bbout->pib_number .
                                                        ', Seri: ' . $bbout->seri_number .
                                                        ', Item: ' . $bbout->item->code . ' ' . $bbout->fg_description .
                                                        ', Qty: ' . $bbout->quantity_remaining . ' ' . $bbout->item->uofm->code,
                                                ];
                                            })->toArray();
                                    }

                                    if ($formId == 2) {
                                        // WIP
                                        return \App\Models\Wip::query()
                                            ->where('wip_quantity_remaining', '>', 0)
                                            ->get()
                                            ->mapWithKeys(function ($wip) {
                                                return [
                                                    $wip->id => 'PIB : ' . $wip->pib_number .' '.$wip->wip_code . ' - ' . $wip->wip_description .' - ' . $wip->document_number, 
                                                ];
                                            })->toArray();
                                    }

                                    return [];
                                })
                                ->searchable()
                                ->reactive()
                                ->required()->afterStateUpdated(function (callable $set, $state, callable $get) {
                                        $formId = $get('form_id');
                                        $bbout = Bbout::find($state); 
                                        $wip = Wip::find($state); 
                                        
                                        if ($formId == 1) {
                                            $set('bbout_id', $bbout->id);
                                            $set('document_number', $bbout->document_number);
                                            $set('document_date', $bbout->document_date);
                                            $set('wips_id', null);
                                            $set('fg_id', $bbout->item->id);
                                            $set('fg_code', $bbout->item->code);
                                            $set('fg_description', $bbout->fg_description);
                                            $set('fg_uofm', $bbout->item_uofm);
                                            $set('fg_quantity', $bbout->use_quantity);
                                            $set('pib_number', $bbout->pib_number);
                                            $set('seri_number', $bbout->seri_number);
                                            // $set('produce_quantity', $bbout->use_quantity);
                                            $set('sub_quantity', $bbout->sub_quantity);
                                            
                                        
                                        }else if($formId == 2){
                                            $set('bbout_id', null);
                                            $set('document_number', $wip->document_number);
                                            $set('document_date', $wip->document_date);
                                            $set('wips_id', $wip->id);
                                            $set('fg_id', $wip->wip_id);
                                            $set('fg_code', $wip->fg_code);
                                            $set('fg_description', $wip->fg_description);
                                            $set('fg_uofm', $wip->fg_uofm_description);
                                            $set('fg_quantity', $wip->wip_quantity);
                                            $set('pib_number', $wip->pib_number);
                                            $set('seri_number', $wip->seri_number);
                                            $set('sub_quantity', 0);
                                        } 
                                        
                                        else {
                                            $set('bbout_id', null);
                                            $set('wips_id', null);
                                            $set('fg_id', null);
                                            $set('fg_code', null);
                                            $set('fg_description', null);
                                            $set('fg_uofm', null);
                                            $set('pib_number', null);
                                            $set('seri_number', null);
                                            $set('sub_quantity', null);
                                        }
                                        
                                    }),
                                    Hidden::make('bbout_id'),
                                    Hidden::make('wips_id'),
                                    Hidden::make('fg_id'),
                                    TextInput::make('fg_code')->label(trans('Kode FG'))->readOnly(),
                                    TextInput::make('fg_description')->label(trans('Nama FG'))->readOnly(),
                                    TextInput::make('fg_uofm')->label(trans('Satuan FG'))->readOnly(),
                                    TextInput::make('fg_quantity')->label(trans('Qty FG'))->numeric()->required()->rule('min:0'),
                                    Select::make('wip_id')->relationship('item', 'code', function(Builder $query){
                                    return $query->where('class_id', 5);
                                    })->label(trans('Item WIP'))->searchable()->preload()->reactive()
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        $item = Item::find($state);
                                        if ($item) {
                                            $set('wip_code', $item->code);
                                            $set('wip_description', $item->description);
                                            $set('wip_uofm', $item->uofm->code);
                
                                        } else {
                                            $set('wip_code', null);
                                            $set('wip_description', null);
                                            $set('wip_uofm', null);
                                        }
                                    }),
                                    Hidden::make('wip_id'),
                                    Hidden::make('wip_code'),
                                    // TextInput::make('wip_code')->label(trans('Kode WIP'))->readOnly(),
                                    TextInput::make('wip_description')->label(trans('Nama WIP'))->readOnly(),
                                    TextInput::make('wip_uofm')->label(trans('Satuan WIP'))->readOnly(),
                                    TextInput::make('wip_quantity')->label(trans('Qty WIP'))->numeric(),
                                    TextInput::make('pib_number')->label(trans('No PIB'))->readOnly()->required(),
                                    TextInput::make('seri_number')->label(trans('No Seri'))->readOnly(),                    
                                    //  Hidden::make('quantity_remaining'),
                                    TextInput::make('sub_quantity')->label(trans('Jumlah dari Subkontrak'))->numeric()->rule('min:0'),
                                    Select::make('storages_id')->relationship('storage', 'storage')->label(trans('Gudang'))->preload()->required(),
                                    Hidden::make('user_id')->default(auth()->id()),
                                    TextInput::make('user_name')
                                    ->label(trans('User'))
                                    ->default(auth()->user()->name) // Mengatur nilai default menjadi nama pengguna saat ini
                                    ->readOnly(),
                        ]),
                ])->columns(1) 
                ->extraAttributes(['class' => 'max-w-full']);
    }
    return $form->schema([
            Select::make('form_id')
                            ->label('Jenis Produksi')
                            ->options([
                                1 => 'BBOUT',
                                2 => 'WIP',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                $set('source_id', null);
                            }),

                                    TextInput::make('document_number')->label(trans('Nomor Invoice'))->required(),
                                    DatePicker::make('document_date')->label(trans('Tanggal Invoice'))->native(false)->closeonDateSelection()->required(),
                            
                                    
                                    TextInput::make('bbout_id')
                                        ->label('Daftar BB Keluar')
                                        ->default(fn ($get) => optional(\App\Models\Bbout::find($get('bbout_id')))->document_number)
                                        ->disabled()
                                        ->dehydrated(false), 
                                    Hidden::make('bbout_id'),
                                    Hidden::make('wips_id'),
                                    Hidden::make('fg_id'),
                                    TextInput::make('fg_code')->label(trans('Kode FG'))->readOnly(),
                                    TextInput::make('fg_description')->label(trans('Nama FG'))->readOnly(),
                                    TextInput::make('fg_uofm')->label(trans('Satuan FG'))->readOnly(),
                                    TextInput::make('fg_quantity')->label(trans('Qty FG'))->numeric()->required()->rule('min:0')->readOnly(),
                                    
                                    TextInput::make('wip_code')
                                        ->label('Item WIP')
                                        ->default(fn ($get) => optional(\App\Models\Wip::find($get('wip_code')))->document_number)
                                        ->disabled()
                                        ->dehydrated(false), 
                                    
                                    Hidden::make('wip_id'),
                                    Hidden::make('wip_code'),
                                    // TextInput::make('wip_code')->label(trans('Kode WIP'))->readOnly(),
                                    TextInput::make('wip_description')->label(trans('Nama WIP'))->readOnly(),
                                    TextInput::make('wip_uofm')->label(trans('Satuan WIP'))->readOnly(),
                                    TextInput::make('wip_quantity')->label(trans('Qty WIP'))->numeric(),
                                    TextInput::make('pib_number')->label(trans('No PIB'))->readOnly()->required(),
                                    TextInput::make('seri_number')->label(trans('No Seri'))->readOnly(),                    
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
                TextColumn::make('fg_code')->label('Kode Barang')->sortable()->searchable()->toggleable(),
                TextColumn::make('fg_description')->label('Nama Barang')->sortable()->searchable()->toggleable(),
                TextColumn::make('fg_uofm')->label('Satuan')->sortable()->searchable()->toggleable(),
                TextColumn::make('fg_quantity')->label('Jumlah dari Produksi')->sortable()->searchable()->toggleable()->formatStateUsing(function ($state) {
                    $formatter = new NumberFormatter('id_ID', NumberFormatter::DECIMAL);
                    return $formatter->format($state);
                }),
                TextColumn::make('sub_quantity')->label('Jumlah dari Subkontrak')->sortable()->searchable()->toggleable()->formatStateUsing(function ($state) {
                    $formatter = new NumberFormatter('id_ID', NumberFormatter::DECIMAL);
                    return $formatter->format($state);
                }),
                TextColumn::make('wip_quantity')->label('Jumlah dari WIP')->sortable()->searchable()->toggleable()->formatStateUsing(function ($state) {
                    $formatter = new NumberFormatter('id_ID', NumberFormatter::DECIMAL);
                    return $formatter->format($state);
                }),
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
