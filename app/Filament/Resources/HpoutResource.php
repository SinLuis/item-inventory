<?php

namespace App\Filament\Resources;

use NumberFormatter;
use App\Exports\HpoutExport;
use App\Filament\Resources\HpoutResource\Pages;
use App\Models\Hpout;
use App\Models\Hpin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class HpoutResource extends Resource
{
    protected static ?string $model = Hpout::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-up';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        if ($form->getOperation() === 'create') {
        return $form
            ->schema([
                Repeater::make('rows')
                    ->label('hp Out')
                    ->defaultItems(1)
                    ->cloneable()
                    ->columns(3)
                    ->schema([
                        TextInput::make('document_number')->label('No. PEB')->required(),
                        DatePicker::make('document_date')->label('Tanggal PEB')->native(false)->closeOnDateSelection()->required(),
                        TextInput::make('sj_number')->label('Nomor Bukti Keluar')->required(),
                        DatePicker::make('sj_date')->label('Tanggal Bukti Keluar')->native(false)->closeOnDateSelection()->required(),
                        Select::make('customer_id')->relationship('customer', 'customer_name')->label('Pembeli / Penerima')->preload()->required(),
                        Select::make('country_id')->relationship('country', 'country')->label('Negara Tujuan')->preload()->required(),
                        Select::make('hpin_id')
                            ->label('Daftar HP Masuk')
                            ->options(function () {
                                $hpins = Hpin::query()->get();

                                if ($hpins->isEmpty()) {
                                    return [];
                                }

                                return $hpins->mapWithKeys(function ($hpin) {
                                    if ($hpin && $hpin->fg_quantity_remaining > 0 && $hpin->bbout != null) {
                                        return [
                                            $hpin->id => $hpin->bbout?->bbin?->document?->code . ': ' . $hpin->pib_number .
                                                ', No Seri: ' . $hpin->seri_number .
                                                ', ' . $hpin->fg_description .
                                                ', Jumlah: ' . $hpin->fg_quantity_remaining .
                                                ' ' . $hpin->item_uofm .
                                                ' - Gudang: ' . $hpin->storage?->storage,
                                        ];
                                    }
                                    return [];
                                })->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $hpin = Hpin::find($state);
  
                                if ($hpin) {
                                    $set('item_id', $hpin->fg_id);
                                    $set('item_code', $hpin->fg_code);
                                    $set('item_description', $hpin->fg_description);
                                    $set('item_longdescription', $hpin->item->long_description ?? null);
                                    $set('item_uofm', $hpin->fg_uofm);
                                    $set('pib_number', $hpin->pib_number);
                                    $set('seri_number', $hpin->seri_number);
                                } else {
                                    $set('item_id', null);
                                    $set('item_code', null);
                                    $set('item_description', null);
                                    $set('item_longdescription', null);
                                    $set('item_uofm', null);
                                    $set('pib_number', null);
                                    $set('seri_number', null);
                                }
                            }),

                        Hidden::make('item_id'),
                        TextInput::make('item_code')->label('Kode Barang')->readOnly(),
                        TextInput::make('item_description')->label('Nama Barang')->readOnly(),
                        TextInput::make('item_longdescription')->label('Deskripsi'),
                        TextInput::make('item_uofm')->label('Satuan')->readOnly(),
                        TextInput::make('pib_number')->label('No PIB')->readOnly(),
                        TextInput::make('seri_number')->label('No Seri')->readOnly(),

                        TextInput::make('total_quantity')->label('Jumlah Quantity')->numeric()->required()->rule('gt:0'),
                        Select::make('currency_id')->relationship('currency', 'currency')->label('Mata Uang')->preload()->required(),
                        TextInput::make('item_amount')->label('Nilai Barang')->numeric()->required()->rule('min:0'),
                        TextInput::make('kurs')->label('Kurs')->numeric()->required()->default(1)->rule('gt:0'),
                        Hidden::make('user_id')->default(auth()->id()),
                        TextInput::make('user_name')
                            ->label('User')
                            ->default(auth()->user()->name)
                            ->readOnly(),
                            ]),
                
            ])->columns(1) 
                ->extraAttributes(['class' => 'max-w-full']);
        }
        return $form
            ->schema([
                TextInput::make('document_number')->label('No. PEB')->required(),
                DatePicker::make('document_date')->label('Tanggal PEB')->native(false)->closeOnDateSelection()->required(),
                TextInput::make('sj_number')->label('Nomor Bukti Keluar')->required(),
                DatePicker::make('sj_date')->label('Tanggal Bukti Keluar')->native(false)->closeOnDateSelection()->required(),
                Select::make('customer_id')->relationship('customer', 'customer_name')->label('Pembeli / Penerima')->preload()->required(),
                Select::make('country_id')->relationship('country', 'country')->label('Negara Tujuan')->preload()->required(),
                Select::make('hpin_id')->relationship('hpin', 'seri_number')->label('HP Masuk')->preload()->required(),

                Hidden::make('item_id'),
                TextInput::make('item_code')->label('Kode Barang')->readOnly(),
                TextInput::make('item_description')->label('Nama Barang')->readOnly(),
                TextInput::make('item_longdescription')->label('Deskripsi'),
                TextInput::make('item_uofm')->label('Satuan')->readOnly(),
                TextInput::make('pib_number')->label('No PIB')->readOnly(),
                TextInput::make('seri_number')->label('No Seri')->readOnly(),

                TextInput::make('total_quantity')->label('Jumlah Quantity')->numeric()->required()->rule('gt:0'),
                Select::make('currency_id')->relationship('currency', 'currency')->label('Mata Uang')->preload()->required(),
                TextInput::make('item_amount')->label('Nilai Barang')->numeric()->required()->rule('min:0'),
                TextInput::make('kurs')->label('Kurs')->numeric()->required()->default(1)->rule('gt:0'),

                Hidden::make('user_id')->default(auth()->id()),
                TextInput::make('user_name')
                    ->label('User')
                    ->default(auth()->user()->name)
                    ->readOnly(),
            ])
            ->columns(3);
        
    }

    public static function create(Create $create): void
    {
        $create->afterSave(function ($record) {
            if (!$record->user_id) {
                $record->user_id = auth()->id();
            }
            $record->user_name = auth()->user()->name;
            $record->save();
        });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')->label('Nomor PEB')->sortable()->searchable(),
                TextColumn::make('document_date')->label('Tanggal PEB')->sortable()->searchable(),
                TextColumn::make('sj_number')->label('Nomor Bukti Pengeluaran Barang')->sortable()->searchable(),
                TextColumn::make('sj_date')->label('Tanggal Bukti Pengeluaran Barang')->sortable()->searchable(),
                TextColumn::make('customer.customer_name')->label('Pembeli/Penerima')->sortable()->searchable(),
                TextColumn::make('country.country')->label('Negara Tujuan')->sortable()->searchable(),
                TextColumn::make('total_quantity')->label('Jumlah')->sortable()->searchable()->formatStateUsing(fn ($state) => (new NumberFormatter('id_ID', NumberFormatter::DECIMAL))->format($state)),
                TextColumn::make('currency.currency')->label('Mata Uang')->sortable()->searchable(),
                TextColumn::make('item_amount')->label('Nilai Barang')->sortable()->searchable()->formatStateUsing(fn ($state) => (new NumberFormatter('id_ID', NumberFormatter::DECIMAL))->format($state)),
            ])
            ->filters([
                Filter::make('document_date_range')
                    ->form([
                        DatePicker::make('start_date')->label('Start Date')->required(),
                        DatePicker::make('end_date')->label('End Date')->required(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['start_date'] && $data['end_date']) {
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
                        ->action(fn ($records) => Excel::download(new HpoutExport($records->pluck('id')->toArray()), 'Hasil Produksi Keluar.xlsx'))
                        ->requiresConfirmation(),
                ])->label('Export'),
            ]);
    }

    public static function getLabel(): string
    {
        return 'HP OUT';
    }
	
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHpouts::route('/'),
            'create' => Pages\CreateHpout::route('/create'),
            'edit' => Pages\EditHpout::route('/{record}/edit'),
        ];
    }
}