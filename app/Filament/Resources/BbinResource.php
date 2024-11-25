<?php

namespace App\Filament\Resources;

use App\Exports\BbinExport;
use App\Filament\Resources\BbinResource\Pages;
use App\Filament\Resources\BbinResource\RelationManagers;
use App\Models\Bbin;
use App\Models\Item;
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
use Maatwebsite\Excel\Facades\Excel;

class BbinResource extends Resource
{
    protected static ?string $model = Bbin::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('document_id')->relationship('document', 'code')->label(trans('Jenis Dokumen'))->preload()->required(),
                TextInput::make('document_number')->label(trans('Nomor Dokumen'))->required(),
                DatePicker::make('document_date')->label(trans('Tanggal Dokumen'))->native(false)->closeonDateSelection()->required(),
                TextInput::make('seri_number')->label(trans('No. Seri Barang'))->required(),
                TextInput::make('reff_number')->label(trans('No. Surat'))->required(),
                DatePicker::make('reff_date')->label(trans('Tanggal Surat'))->native(false)->closeonDateSelection()->required(),
                Select::make('item_id')->relationship('item', 'code', function(Builder $query){
                    return $query->where('class_id', 1);
                })->label(trans('Item ID'))->required()->searchable()->preload()->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    $item = Item::find($state);
                    if ($item) {
                        $set('item_description', $item->description);
                        $set('item_longdescription', $item->long_description);
                        $set('item_uofm', $item->uofm->code);
                    } else {
                        $set('item_description', null);
                        $set('item_longdescription', null);
                        $set('item_uofm', null);
                    }
                }),
                TextInput::make('item_description')->label(trans('Nama Item'))->readOnly(),
                TextInput::make('item_longdescription')->label(trans('Deskripsi'))->readOnly(),
                TextInput::make('item_uofm')->label(trans('Satuan Item'))->readOnly(),
                TextInput::make('total_container')->label('Jumlah Kontainer')->numeric()->required()->rule('numeric'),
                TextInput::make('total_quantity')->label('Jumlah Quantity')->numeric()->required()->rule('numeric')->rule('gt:0')->afterStateUpdated(function ($state, callable $set) {
                   $set('quantity_remaining', $state);
                }),
                Hidden::make('quantity_remaining'),
                Select::make('currency_id')->relationship('currency', 'currency')->label(trans('Mata Uang'))->preload()->required(),
                TextInput::make('kurs')->label('Kurs')->numeric()->required()->rule('numeric')->default(1)->rule('gt:0'),
                TextInput::make('item_amount')->label('Nilai Barang')->numeric()->required()->rule('numeric')->rule('min:0'),
                Select::make('storages_id')->relationship('storage', 'storage')->label(trans('Gudang'))->preload()->required(),
                Select::make('subkontrak_id')->relationship('subsupplier', 'supplier_name', function(Builder $query){
                    return $query->where('class_id', 2);
                })->label(trans('Penerima Subkontrak'))->preload()->searchable(),
                Select::make('supplier_id')->relationship('supsupplier', 'supplier_name', function(Builder $query){
                    return $query->where('class_id', 1);
                })->label(trans('Pemasok Pengirim'))->preload()->searchable()->required(),
                Select::make('country_id')->relationship('country', 'country')->label(trans('Negara'))->preload()->required(),
                Hidden::make('user_id')->default(auth()->id()),
                TextInput::make('user_name')
                ->label(trans('User'))
                ->default(auth()->user()->name) // Mengatur nilai default menjadi nama pengguna saat ini
                ->readOnly(),
            ])->columns(3);
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
        return 'BB IN';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document.code')->label('Jenis Dok')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_number')->label('Nomor Dok')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_date')->label('Tanggal Dok')->sortable()->searchable()->toggleable(),
                TextColumn::make('seri_number')->label('No Seri')->sortable()->searchable()->toggleable(),
                TextColumn::make('reff_number')->label('Nomor Invoice')->sortable()->searchable()->toggleable(),
                TextColumn::make('reff_date')->label('Tanggal Invoice')->sortable()->searchable()->toggleable(),
                TextColumn::make('item.code')->label('Kode Barang')->sortable()->searchable()->toggleable(),
                TextColumn::make('item_description')->label('Nama Barang')->sortable()->searchable()->toggleable(),
                TextColumn::make('item_uofm')->label('Satuan')->sortable()->searchable()->toggleable(),
                TextColumn::make('total_container')->label('Jumlah Container')->sortable()->searchable()->toggleable(),
                TextColumn::make('currency.currency')->label('Mata Uang')->sortable()->searchable()->toggleable(),
                TextColumn::make('item_amount')->label('Nilai Barang')->sortable()->searchable()->toggleable(),
                TextColumn::make('storage.storage')->label('Gudang')->sortable()->searchable()->toggleable(),
                TextColumn::make('subsupplier.supplier_name')->label('Penerima Subkontrak')->sortable()->searchable()->toggleable(),
                TextColumn::make('supsupplier.supplier_name')->label('Pemasok Pengirim')->sortable()->searchable()->toggleable(),
                TextColumn::make('country.country')->label('Negara Asal')->sortable()->searchable()->toggleable(),
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
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('export')
                        ->label('Export to Excel')
                        ->action(function ($records) {
                            $recordIds = $records->pluck('id')->toArray(); // Extract only the IDs
                            return Excel::download(new BbinExport($recordIds), 'Bahan Baku Masuk.xlsx');
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
            'index' => Pages\ListBbins::route('/'),
            'create' => Pages\CreateBbin::route('/create'),
            'edit' => Pages\EditBbin::route('/{record}/edit'),
            
        ];
    }
}
