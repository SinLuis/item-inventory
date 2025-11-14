<?php

namespace App\Filament\Resources;

use NumberFormatter;
use App\Exports\BbinExport;
use App\Filament\Resources\BbinResource\Pages;
use App\Models\Bbin;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class BbinResource extends Resource
{
    protected static ?string $model = Bbin::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {

    if ($form->getOperation() === 'create') {
    return $form
        ->schema([
            Repeater::make('rows')
                ->label('Data Dokumen')
                ->cloneable() // adds "Duplicate" button
                // ->createItemButtonLabel('Tambah Dokumen')
                ->defaultItems(1)
                ->columns(3) // 🔑 spread fields into 3 columns
                ->schema([
                    Select::make('document_id')
                        ->relationship('document', 'code')
                        ->label(trans('Jenis Dokumen'))
                        ->preload()
                        ->required(),

                    TextInput::make('document_number')
                        ->label(trans('Nomor Dokumen'))
                        ->required(),

                    DatePicker::make('document_date')
                        ->label(trans('Tanggal Dokumen'))
                        ->native(false)
                        ->closeOnDateSelection()
                        ->required(),

                    TextInput::make('seri_number')
                        ->label(trans('No. Seri Barang'))
                        ->required(),

                    TextInput::make('reff_number')
                        ->label(trans('No. Surat'))
                        ->required(),

                    DatePicker::make('reff_date')
                        ->label(trans('Tanggal Surat'))
                        ->native(false)
                        ->closeOnDateSelection()
                        ->required(),

                    Select::make('item_id')
                        ->relationship('item', 'code', fn (Builder $query) => $query->where('class_id', 1))
                        ->label(trans('Item ID'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $item = Item::find($state);
                            if ($item) {
                                $set('item_description', $item->description);
                                $set('item_longdescription', $item->long_description);
                                $set('item_uofm', $item->uofm->code ?? null);
                            } else {
                                $set('item_description', null);
                                $set('item_longdescription', null);
                                $set('item_uofm', null);
                            }
                        }),

                    TextInput::make('item_description')->label(trans('Nama Item'))->readOnly(),
                    TextInput::make('item_longdescription')->label(trans('Deskripsi')),
                    TextInput::make('item_uofm')->label(trans('Satuan Item'))->readOnly(),

                    TextInput::make('total_container')
                        ->label('Jumlah Kontainer')
                        ->numeric()
                        ->required()
                        ->rule('numeric')
                        ->rule('min:0'),

                    TextInput::make('total_quantity')
                        ->label('Jumlah Quantity')
                        ->numeric()
                        ->required()
                        ->rule('numeric')
                        ->rule('gt:0')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('quantity_remaining', $state)),

                    Hidden::make('quantity_remaining'),

                    Select::make('currency_id')
                        ->relationship('currency', 'currency')
                        ->label(trans('Mata Uang'))
                        ->preload()
                        ->required(),

                    TextInput::make('kurs')
                        ->label('Kurs')
                        ->numeric()
                        ->required()
                        ->default(1)
                        ->rule('gt:0'),

                    TextInput::make('item_amount')
                        ->label('Nilai Barang')
                        ->numeric()
                        ->required()
                        ->rule('numeric')
                        ->rule('min:0'),

                    Select::make('storages_id')
                        ->relationship('storage', 'storage')
                        ->label(trans('Gudang'))
                        ->preload()
                        ->required(),

                    Select::make('subkontrak_id')
                        ->relationship('subsupplier', 'supplier_name', fn (Builder $query) => $query->where('class_id', 2))
                        ->label(trans('Penerima Subkontrak'))
                        ->preload()
                        ->searchable(),

                    Select::make('supplier_id')
                        ->relationship('supsupplier', 'supplier_name', fn (Builder $query) => $query->where('class_id', 1))
                        ->label(trans('Pemasok Pengirim'))
                        ->preload()
                        ->searchable()
                        ->required(),

                    Select::make('country_id')
                        ->relationship('country', 'country')
                        ->label(trans('Negara'))
                        ->preload()
                        ->searchable()
                        ->required(),

                    Hidden::make('user_id')->default(fn () => auth()->id()),

                    TextInput::make('user_name')
                        ->label(trans('User'))
                        ->default(fn () => auth()->user()?->name)
                        ->readOnly(),
                ]),
        ])
        ->columns(1) // 🔑 make repeater span the full width
        ->extraAttributes(['class' => 'max-w-full']); // 🔑 remove Filament’s width limit
        }

        return $form->schema([
            Select::make('document_id')
                ->relationship('document', 'code')
                ->label('Jenis Dokumen')
                ->preload()
                ->required(),

            TextInput::make('document_number')
                ->label('Nomor Dokumen')
                ->required(),

            DatePicker::make('document_date')
                ->label('Tanggal Dokumen')
                ->native(false)
                ->closeOnDateSelection()
                ->required(),

            TextInput::make('seri_number')
                ->label('No. Seri Barang')
                ->required(),

            TextInput::make('reff_number')
                ->label('No. Surat')
                ->required(),

            DatePicker::make('reff_date')
                ->label('Tanggal Surat')
                ->native(false)
                ->closeOnDateSelection()
                ->required(),

            Select::make('item_id')
                ->relationship('item', 'code', fn (Builder $query) => $query->where('class_id', 1))
                ->label('Item ID')
                ->required()
                ->searchable()
                ->preload()
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    $item = Item::find($state);
                    if ($item) {
                        $set('item_description', $item->description);
                        $set('item_longdescription', $item->long_description);
                        $set('item_uofm', $item->uofm->code ?? null);
                    } else {
                        $set('item_description', null);
                        $set('item_longdescription', null);
                        $set('item_uofm', null);
                    }
                }),

            TextInput::make('item_description')->label('Nama Item')->readOnly(),
            TextInput::make('item_longdescription')->label('Deskripsi'),
            TextInput::make('item_uofm')->label('Satuan Item')->readOnly(),

            TextInput::make('total_container')
                ->label('Jumlah Kontainer')
                ->numeric()
                ->required(),

            TextInput::make('total_quantity')
                ->label('Jumlah Quantity')
                ->numeric()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn ($state, callable $set) => $set('quantity_remaining', $state)),

            Hidden::make('quantity_remaining'),

            Select::make('currency_id')
                ->relationship('currency', 'currency')
                ->label('Mata Uang')
                ->preload()
                ->required(),

            TextInput::make('kurs')
                ->label('Kurs')
                ->numeric()
                ->required()
                ->default(1),

            TextInput::make('item_amount')
                ->label('Nilai Barang')
                ->numeric()
                ->required(),

            Select::make('storages_id')
                ->relationship('storage', 'storage')
                ->label('Gudang')
                ->preload()
                ->required(),

            Select::make('subkontrak_id')
                ->relationship('subsupplier', 'supplier_name', fn (Builder $query) => $query->where('class_id', 2))
                ->label('Penerima Subkontrak')
                ->preload()
                ->searchable(),

            Select::make('supplier_id')
                ->relationship('supsupplier', 'supplier_name', fn (Builder $query) => $query->where('class_id', 1))
                ->label('Pemasok Pengirim')
                ->preload()
                ->searchable()
                ->required(),

            Select::make('country_id')
                ->relationship('country', 'country')
                ->label('Negara')
                ->preload()
                ->searchable()
                ->required(),

            Hidden::make('user_id')->default(fn () => auth()->id()),

            TextInput::make('user_name')
                ->label('User')
                ->default(fn () => auth()->user()?->name)
                ->readOnly(),
        ]);
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
                 TextColumn::make('quantity_remaining')->label('Stock')->sortable()->searchable()->toggleable(),
                TextColumn::make('item_uofm')->label('Satuan')->sortable()->searchable()->toggleable(),
                TextColumn::make('total_container')->label('Jumlah Container')->sortable()->searchable()->toggleable(),
                TextColumn::make('currency.currency')->label('Mata Uang')->sortable()->searchable()->toggleable(),
                TextColumn::make('item_amount')
                    ->label('Nilai Barang')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        $formatter = new NumberFormatter('id_ID', NumberFormatter::DECIMAL);
                        return $formatter->format($state);
                    }),
                TextColumn::make('storage.storage')->label('Gudang')->sortable()->searchable()->toggleable(),
                TextColumn::make('subsupplier.supplier_name')->label('Penerima Subkontrak')->sortable()->searchable()->toggleable(),
                TextColumn::make('supsupplier.supplier_name')->label('Pemasok Pengirim')->sortable()->searchable()->toggleable(),
                TextColumn::make('country.country')->label('Negara Asal')->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                Filter::make('document_date_range')
                    ->form([
                        DatePicker::make('start_date')->label('Start Date')->required(),
                        DatePicker::make('end_date')->label('End Date')->required(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['start_date']) && !empty($data['end_date'])) {
                            $query->whereBetween('document_date', [$data['start_date'], $data['end_date']]);
                        }
                    }),
            ])
            ->filtersFormColumns(1)
            ->deferFilters()
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->headerActions([ // <-- correct casing
                Action::make('export')
                    ->label('Export BBIN')
                    ->action(function (array $data) {
                        $startDate = $data['start_date'] ?? null;
                        $endDate   = $data['end_date'] ?? null;

                        $query = Bbin::query();
                        if ($startDate && $endDate) {
                            $query->whereBetween('document_date', [$startDate, $endDate]);
                        }

                        $recordIds = $query->pluck('id')->toArray();

                        return Excel::download(new BbinExport($recordIds), 'Bahan Baku Masuk.xlsx');
                    })
                    ->form([
                        DatePicker::make('start_date')->label('Start Date')->required(),
                        DatePicker::make('end_date')->label('End Date')->required(),
                    ])
                    ->requiresConfirmation(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBbins::route('/'),
            'create' => Pages\CreateBbin::route('/create'),
            'edit'   => Pages\EditBbin::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): string
    {
        return 'BB IN';
    }
}
