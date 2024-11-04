<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MutationoutResource\Pages;
use App\Filament\Resources\MutationoutResource\RelationManagers;
use App\Models\Bbin;
use App\Models\Mutationout;
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
use Maatwebsite\Excel\Facades\Excel;

class MutationoutResource extends Resource
{
    protected static ?string $model = Mutationout::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';
    protected static ?string $navigationGroup = 'Storages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('document_number')->label(trans('Nomor Bukti Pengeluaran'))->required(),
                DatePicker::make('document_date')->label(trans('Tanggal Keluar'))->native(false)->required(),

                Select::make('bbin') 
                        ->label(trans('Search'))
                        ->options(function () {
                              return Bbin::query()
                                ->get()
                                ->mapWithKeys(function ($bbin) {
                                    if($bbin != null){
                                        return [
                                            $bbin->id => 'PIB: ' . $bbin->document_number . ' - ' . $bbin->item->code . ' - ' . $bbin->item_description . ' - ' . $bbin->total_quantity . ' ' . $bbin->item->uofm->code . ' - ' . $bbin->storage->storage
                                        ]; 
                                    }
                                    else{
                                        return null;
                                    }
                                    
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->required()->reactive()->afterStateUpdated(function (callable $set, $state) {
                            $bbin = Bbin::find($state); 
                           
                            if ($bbin) {
                                $set('bbin_num', $bbin->document_number);
                                $set('bbin_seri', $bbin->seri_number);
                                $set('item_id', $bbin->item->code);
                                $set('item_description', $bbin->item->description);
                                $set('item_uofm', $bbin->item->uofm->code);
                                $set('storagesout_id', $bbin->storages_id);
                                $set('storagesout_desc', $bbin->storage->storage);
                    
                            } else {
                                $set('bbin_num', null);
                                $set('bbin_seri', null);
                                $set('item_id', null);
                                $set('item_description', null);
                                $set('item_uofm', null);
                                $set('storagesout_id', null);
                                $set('storagesout_desc', null);
                            }
                            
                        }),

                        TextInput::make('bbin_num')->label(trans('PIB'))->readOnly(),
                        TextInput::make('bbin_seri')->label(trans('No Seri'))->readOnly(),
                        TextInput::make('item_id')->label(trans('Kode Barang'))->readOnly(),
                        TextInput::make('item_description')->label(trans('Nama Barang'))->readOnly(),
                        TextInput::make('item_uofm')->label(trans('Satuan'))->readOnly(),
                        Hidden::make('storagesout_id'),
                        TextInput::make('storagesout_desc')->label('Gudang Asal')->readOnly(),
                        Select::make('storagesin_id')->relationship('storagein', 'storage')->label(trans('Gudang Tujuan'))->required()->searchable()->preload()->reactive(),
                        TextInput::make('move_quantity')->label('Jumlah Dipindahkan')->numeric()->required()->rule('numeric'),
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
                TextColumn::make('document_date')->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                Filter::make('document_date_range')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required(),
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getLabel(): string
    {
        return 'Mutation Out';
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
            'index' => Pages\ListMutationouts::route('/'),
            'create' => Pages\CreateMutationout::route('/create'),
            'edit' => Pages\EditMutationout::route('/{record}/edit'),
        ];
    }
}
