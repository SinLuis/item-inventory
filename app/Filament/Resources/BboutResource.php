<?php

namespace App\Filament\Resources;

use NumberFormatter; 
use App\Filament\Resources\BboutResource\Pages; 
use App\Filament\Resources\BboutResource\RelationManagers; 
use App\Models\Bbout; 
use App\Models\Bbin; 
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
use Filament\Forms\Components\Repeater; 
use Filament\Resources\Pages\Page; 
use Filament\Resources\Pages\CreateRecord; 
use Filament\Infolists\Infolist; 
use Filament\Infolists\Components\TextEntry; 
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Database\Eloquent\SoftDeletingScope; 
use Illuminate\Support\Facades\Log; 
use Maatwebsite\Excel\Facades\Excel;

class BboutResource extends Resource
{
    protected static ?string $model = Bbout::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 3;
    
    public static function form(Form $form): Form
    {
        if ($form->getOperation() === 'create') {
            // CREATE pakai repeater
            return $form
                ->schema([
                    Repeater::make('rows')
                        ->label('Data Dokumen')
                        ->cloneable()
                        ->defaultItems(1)
                        ->columns(3)
                        ->schema([
                            TextInput::make('document_number')->label('Nomor Bukti Pengeluaran')->required(),
                            DatePicker::make('document_date')->label('Tanggal Keluar')->native(false)->closeOnDateSelection()->required(),

                            Select::make('bbin_id')
                                ->label('Daftar BB Masuk')
                                ->options(function () {
                                    $bbins = Bbin::query()->get();
                                    Log::info('BBins Retrieved:', $bbins->toArray());

                                    return $bbins->mapWithKeys(function ($bbin) {
                                        if ($bbin && $bbin->quantity_remaining > 0) {
                                            return [
                                                $bbin->id => $bbin->document->code . ': ' . $bbin->document_number .
                                                    ', No Seri: ' . $bbin->seri_number .
                                                    ', ' . $bbin->item->description .
                                                    ', Jumlah: ' . $bbin->quantity_remaining .
                                                    ' ' . $bbin->item->uofm->code .
                                                    ' - Gudang: ' . $bbin->storage->storage
                                            ];
                                        }
                                        return [];
                                    })->toArray();
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (callable $set, $state) {
                                    $bbin = Bbin::find($state); 
                                    if ($bbin) {
                                        $set('pib_number', $bbin->document_number);
                                        $set('seri_number', $bbin->seri_number);
                                        $set('item_id', $bbin->item->id);
                                        $set('item_code', $bbin->item->code);
                                        $set('item_description', $bbin->item->description);
                                        $set('item_uofm', $bbin->item->uofm->code);
                                        if($bbin->subsupplier == null){
                                            $set('subkontrak_name', null);
                                            $set('subkontrak_id', null);
                                        }else{
                                            $set('subkontrak_name', $bbin->subsupplier->supplier_name);
                                            $set('subkontrak_id', $bbin->subsupplier->id);
                                        }
                                    } else {
                                        $set('pib_number', null);
                                        $set('seri_number', null);
                                        $set('item_id', null);
                                        $set('item_code', null);
                                        $set('item_description', null);
                                        $set('item_uofm', null);
                                        $set('subkontrak_name', null);
                                        $set('subkontrak_id', null);
                                    }
                                }),

                            TextInput::make('pib_number')->label('PIB')->readOnly(),
                            TextInput::make('seri_number')->label('No Seri')->readOnly(),
                            Hidden::make('item_id'),
                            TextInput::make('item_code')->label('Kode Barang')->readOnly(),
                            TextInput::make('item_description')->label('Nama Barang')->readOnly(),
                            TextInput::make('item_uofm')->label('Satuan')->readOnly(),

                            TextInput::make('use_quantity')->label('Jumlah Digunakan')->numeric()->required()->rule('numeric')->rule('min:0'),
                            TextInput::make('sub_quantity')->label('Jumlah Disubkontrakan')->numeric()->required()->default(0)->rule('numeric')->rule('min:0'),

                            TextInput::make('subkontrak_name')->label('Subkontrak')->readOnly(),
                            Hidden::make('subkontrak_id'),
                            TextInput::make('notes')->label('Keterangan Lain'),

                            Select::make('fg_id')
                                ->relationship('item', 'description', function(Builder $query){
                                    return $query->where('class_id', 2);
                                })
                                ->label('Kode Barang Jadi')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->reactive()
                                ->afterStateUpdated(function (callable $set, $state) {
                                    $item = Item::find($state);
                                    if ($item) {
                                        $set('fg_code', $item->code);
                                        $set('fg_description', $item->description);
                                    } else {
                                        $set('fg_code', null);
                                        $set('fg_description', null);
                                    }
                                }),

                            Hidden::make('fg_code'),
                            Hidden::make('fg_description'),
                            Hidden::make('user_id')->default(auth()->id()),
                            TextInput::make('user_name')
                                ->label('User')
                                ->default(auth()->user()->name)
                                ->readOnly(),
                        ]),
                ])
                ->columns(1)
                ->extraAttributes(['class' => 'max-w-full']);
        }

        // EDIT pakai form biasa (bukan repeater)
        return $form->schema([
            TextInput::make('document_number')->label('Nomor Bukti Pengeluaran')->required(),
            DatePicker::make('document_date')->label('Tanggal Keluar')->native(false)->closeOnDateSelection()->required(),

            Select::make('bbin_id')
                ->relationship('bbin', 'document_number')
                ->label('Daftar BB Masuk')
                ->required(),

            TextInput::make('pib_number')->label('PIB')->readOnly(),
            TextInput::make('seri_number')->label('No Seri')->readOnly(),
            TextInput::make('item_code')->label('Kode Barang')->readOnly(),
            TextInput::make('item_description')->label('Nama Barang')->readOnly(),
            TextInput::make('item_uofm')->label('Satuan')->readOnly(),

            TextInput::make('use_quantity')->label('Jumlah Digunakan')->numeric()->required(),
            TextInput::make('sub_quantity')->label('Jumlah Disubkontrakan')->numeric()->required(),

            TextInput::make('subkontrak_name')->label('Subkontrak')->readOnly(),
            TextInput::make('notes')->label('Keterangan Lain'),

            Select::make('fg_id')
                ->relationship('item', 'description', fn (Builder $query) => $query->where('class_id', 2))
                ->label('Kode Barang Jadi')
                ->required()
                ->searchable()
                ->preload(),

            Hidden::make('user_id')->default(auth()->id()),
            TextInput::make('user_name')->label('User')->default(auth()->user()->name)->readOnly(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')->label('Nomor')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_date')->label('Tanggal')->sortable()->searchable()->toggleable(),  
                TextColumn::make('item_code')->label('Kode Barang')->sortable()->searchable()->toggleable(),
                TextColumn::make('item_description')->label('Nama Barang')->sortable()->searchable()->toggleable(),
                TextColumn::make('item_uofm')->label('Satuan')->sortable()->searchable()->toggleable(),
                TextColumn::make('use_quantity')->label('Jumlah Digunakan')->sortable()->searchable()->toggleable()
                    ->formatStateUsing(fn ($state) => (new NumberFormatter('id_ID', NumberFormatter::DECIMAL))->format($state)),
                TextColumn::make('sub_quantity')->label('Jumlah Disubkontrakan')->sortable()->searchable()->toggleable()
                    ->formatStateUsing(fn ($state) => (new NumberFormatter('id_ID', NumberFormatter::DECIMAL))->format($state)),
                TextColumn::make('subsupplier.supplier_name')->label('Penerima Subkontrak')->sortable()->searchable()->toggleable(),
                TextColumn::make('notes')->label('Keterangan')->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                Filter::make('document_date_range')
                    ->form([
                        DatePicker::make('start_date')->label('Start Date')->required()->closeOnDateSelection(),
                        DatePicker::make('end_date')->label('End Date')->required()->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['start_date']) && isset($data['end_date'])) {
                            $query->whereBetween('document_date', [$data['start_date'], $data['end_date']]);
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
                            $recordIds = $records->pluck('id')->toArray();
                            return Excel::download(new BboutExport($recordIds), 'Bahan Baku Keluar.xlsx');
                        })
                        ->requiresConfirmation(),
                ])->label('Export'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBbouts::route('/'),
            'create' => Pages\CreateBbout::route('/create'),
            'edit' => Pages\EditBbout::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): string
    {
        return 'BB OUT';
    }
}
