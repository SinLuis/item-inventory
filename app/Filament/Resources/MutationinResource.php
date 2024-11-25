<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MutationinResource\Pages;
use App\Filament\Resources\MutationinResource\RelationManagers;
use App\Models\Mutationout;
use App\Models\Mutationin;
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

class MutationinResource extends Resource
{
    protected static ?string $model = Mutationin::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-down';
    protected static ?string $navigationGroup = 'Storages';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('document_number')->label(trans('Nomor Bukti Pengeluaran'))->required(),
                DatePicker::make('document_date')->label(trans('Tanggal Keluar'))->native(false)->closeonDateSelection()->required(),

                Select::make('mutout')
                ->label(trans('Search'))
                ->options(function () {
                    $mutationouts = Mutationout::whereDoesntHave('mutationin')
                        ->get();
                    
                    // Debugging
                     // Menampilkan data Mutationout yang tidak terkait dengan Mutationin
                    
                    return $mutationouts->mapWithKeys(function ($mutout) {
                        return [
                            $mutout->id => 'PIB: ' . $mutout->pib_number . ' - ' . $mutout->item_id . ' - ' . $mutout->item_description . ' - ' . $mutout->move_quantity . ' ' . $mutout->item_uofm . ' - ' . $mutout->storagesout_desc
                        ];
                    })->toArray();
                })
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    $mutout = Mutationout::find($state);
                    
                    if ($mutout) {
                        $set('mutationout_id', $mutout->id);
                        $set('bbin_id', $mutout->bbin_id);
                        $set('pib_number', $mutout->pib_number);
                        $set('seri_number', $mutout->seri_number);
                        $set('item_id', $mutout->item_id);
                        $set('item_code', $mutout->item_code);
                        $set('item_description', $mutout->item_description);
                        $set('item_uofm', $mutout->item_uofm);
                        $set('storagesout_id', $mutout->storagesout_id);
                        $set('storagesout_desc', $mutout->storagesout_desc);
                        $set('storagesin_id', $mutout->storagesin_id);
                        $set('storagesin_desc', $mutout->storagein->storage);
                    }
                            // dd($mutout);
                        }),
                        Hidden::make('mutationout_id'),
                        Hidden::make('bbin_id'),
                        Hidden::make('item_id'),
                        TextInput::make('pib_number')->label(trans('PIB'))->readOnly(),
                        TextInput::make('seri_number')->label(trans('No Seri'))->readOnly(),
                        TextInput::make('item_code')->label(trans('Kode Barang'))->readOnly(),
                        TextInput::make('item_description')->label(trans('Nama Barang'))->readOnly(),
                        TextInput::make('item_uofm')->label(trans('Satuan'))->readOnly(),
                        Hidden::make('storagesout_id'),
                        TextInput::make('storagesout_desc')->label('Gudang Asal')->readOnly(),
                        Hidden::make('storagesin_id')->label('Gudang Tujuan'),
                        TextInput::make('storagesin_desc')->label('Gudang Asal')->readOnly(),
                        // Select::make('storagesin_id')->relationship('storagein', 'storage')->label(trans('Gudang Tujuan'))->required()->searchable()->preload()->reactive(),
                        TextInput::make('move_quantity')->label('Jumlah Dipindahkan')->numeric()->required()->rule('numeric')->rule('gt:0'),
                        TextInput::make('notes')->label(trans('Keterangan Lain')),
                        Hidden::make('user_id')->default(auth()->id()),
                        TextInput::make('user_name')
                        ->label(trans('User'))
                        ->default(auth()->user()->name) // Mengatur nilai default menjadi nama pengguna saat ini
                        ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')->sortable()->searchable()->toggleable(),
                TextColumn::make('pib_number')->sortable()->searchable()->toggleable(),
                TextColumn::make('seri_number')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_date')->sortable()->searchable()->toggleable(),
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
                Tables\Actions\ViewAction::make()
                ->form(fn () => [
                    Forms\Components\Grid::make(2) // 2 columns
                        ->schema([
                            TextInput::make('document_number')->label(trans('Nomor Bukti Pengeluaran'))->readOnly(),
                            DatePicker::make('document_date')->label(trans('Tanggal Keluar'))->readOnly(),
                            TextInput::make('pib_number')->label(trans('PIB'))->readOnly(),
                            TextInput::make('seri_number')->label(trans('No Seri'))->readOnly(),
                            TextInput::make('item_code')->label(trans('Kode Barang'))->readOnly(),
                            TextInput::make('item_description')->label(trans('Nama Barang'))->readOnly(),
                            TextInput::make('item_uofm')->label(trans('Satuan'))->readOnly(),
                            TextInput::make('storagesout_desc')->label('Gudang Asal')->readOnly(),
                            TextInput::make('storagesin_desc')->label('Gudang Tujuan')->readOnly(),
                            TextInput::make('move_quantity')->label('Jumlah Dipindahkan')->numeric()->readOnly(),
                            TextInput::make('notes')->label(trans('Keterangan Lain'))->readOnly(),
                            TextInput::make('user_name')->label(trans('User'))->readOnly(),
                        ]),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getLabel(): string
    {
        return 'Mutation In';
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
            'index' => Pages\ListMutationins::route('/'),
            'create' => Pages\CreateMutationin::route('/create'),
            'edit' => Pages\EditMutationin::route('/{record}/edit'),
        ];
    }
}
