<?php

namespace App\Filament\Resources;

use App\Exports\BboutExport;
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
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;

class BboutResource extends Resource
{
    protected static ?string $model = Bbout::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 3;
    

        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    TextInput::make('document_number')->label(trans('Nomor Bukti Pengeluaran'))->required(),
                    DatePicker::make('document_date')->label(trans('Tanggal Keluar'))->native(false)->required(),
                    Select::make('bbin') 
                        ->label(trans('Daftar BB Masuk'))
                        ->options(function () {
                              return Bbin::query()
                                ->get()
                                ->mapWithKeys(function ($bbin) {
                                    if($bbin != null){
                                        return [
                                            $bbin->id => 'PIB: ' . $bbin->document_number . ', No Seri: ' . $bbin->seri_number . ', ' . $bbin->item->description . ', Jumlah: ' . $bbin->total_quantity . ' ' . $bbin->item->uofm->code . ' - Gudang: ' . $bbin->storage->storage
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
                                if($bbin->subsupplier == null){
                                    $set('subkontrak_name', null);
                                    $set('subkontrak_id', null);
                                }else{
                                    $set('subkontrak_name', $bbin->subsupplier->supplier_name);
                                    $set('subkontrak_id', $bbin->subsupplier->id);
                                }
                                
                                
                                
                            
                            } else {
                                $set('bbin_num', null);
                                $set('bbin_seri', null);
                                $set('item_id', null);
                                $set('item_description', null);
                                $set('item_uofm', null);
                                $set('subkontrak_name', null);
                                $set('subkontrak_id', null);
                            }
                            
                        }),
  
                        // TextInput::make('bbin')->label(trans('PIB'))->readOnly(function ($get){
                        //     if ($get('bbin') != null) {
                        //         dd($bbin = Bbin::where('document_number', $get('bbin'))->first()->pluck('document_number', 'id'));
                        //     }
                        // }),
                        TextInput::make('bbin_num')->label(trans('PIB'))->readOnly(),
                        TextInput::make('bbin_seri')->label(trans('No Seri'))->readOnly(),
                        TextInput::make('item_id')->label(trans('Kode Barang'))->readOnly(),
                        TextInput::make('item_description')->label(trans('Nama Barang'))->readOnly(),
                        TextInput::make('item_uofm')->label(trans('Satuan'))->readOnly(),
                        TextInput::make('use_quantity')->label('Jumlah Digunakan')->numeric()->required()->rule('numeric'),
                        TextInput::make('sub_quantity')->label('Jumlah Disubkontrakan')->numeric()->default(0)->rule('numeric'),
                        TextInput::make('subkontrak_name')->label(trans('Subkontrak'))->readOnly(),
                        Hidden::make('subkontrak_id'),
                        TextInput::make('notes')->label(trans('Keterangan Lain')),
                        Select::make('fg_id')->relationship('item', 'description', function(Builder $query){
                            return $query->where('class_id', 2);
                        })->label(trans('Kode Barang Jadi'))->required()->searchable()->preload()->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $item = Item::find($state);
                            if ($item) {
                                $set('fg_description', $item->description);
                             
                            } else {
                                $set('fg_description', null);
                            }
                        }),
                        Hidden::make('fg_description'),
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
            return 'BB OUT';
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
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        // Tables\Actions\DeleteBulkAction::make(),
                        BulkAction::make('export')
                        ->label('Export to Excel')
                        ->action(fn () => Excel::download(new BboutExport, 'Bbout.xlsx'))
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
                'index' => Pages\ListBbouts::route('/'),
                'create' => Pages\CreateBbout::route('/create'),
                'edit' => Pages\EditBbout::route('/{record}/edit'),
                
            ];
        }
}
