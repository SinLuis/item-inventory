<?php

namespace App\Filament\Resources;

use App\Exports\HpoutExport;
use App\Filament\Resources\HpoutResource\Pages;
use App\Filament\Resources\HpoutResource\RelationManagers;
use App\Models\Hpout;
use App\Models\Hpin;
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

class HpoutResource extends Resource
{
    protected static ?string $model = Hpout::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-up';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('document_number')->label(trans('No. PEB'))->required(),
                DatePicker::make('document_date')->label(trans('Tanggal PEB'))->native(false)->required(),
                TextInput::make('sj_number')->label(trans('Nomor Bukti Keluar'))->required(),
                DatePicker::make('sj_date')->label(trans('Tanggal Bukti Keluar'))->native(false)->required(),
                Select::make('customer_id')->relationship('customer', 'customer_name')->label(trans('Pembeli / Penerima'))->preload()->required(),
                Select::make('country_id')->relationship('country', 'country')->label(trans('Negara Tujuan'))->preload()->required(),

                // 1 Open
                Select::make('hpin') 
                ->label(trans('Daftar HP Masuk'))
                ->options(function () {
                      return Hpin::query()
                        ->get()
                        ->mapWithKeys(function ($hpin) {
                            if($hpin != null){
                                return [
                                    $hpin->id => 'PIB: ' . $hpin->bbin_num . ', No Seri: ' . $hpin->seri_num . ', ' . $hpin->item_description . ', Jumlah: ' . $hpin->produce_quantity . ' ' . $hpin->item_uofm 
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
                    $hpin = Hpin::find($state); 
                    
                    if ($hpin) {
                        $set('item_id', $hpin->item_id);
                        $set('item_description', $hpin->item_description);
                        $set('item_uofm', $hpin->item_uofm);
                        $set('no_pib', $hpin->bbin_num);
                        $set('seri_number', $hpin->seri_num);
                        
                    
                    } else {
                        $set('item_id', null);
                        $set('item_description', null);
                        $set('item_uofm', null);
                        $set('no_pib', null);
                        $set('seri_number', null);

                    }
                    
                }),
                // 1 Close

                TextInput::make('item_id')->label(trans('Kode Barang'))->readOnly(),
                TextInput::make('item_description')->label(trans('Nama Barang'))->readOnly(),
                TextInput::make('item_longdescription')->label(trans('Deskripsi'))->required(),
                TextInput::make('item_uofm')->label(trans('Satuan'))->readOnly(),
                TextInput::make('no_pib')->label(trans('No PIB'))->readOnly(),
                TextInput::make('seri_number')->label(trans('No Seri'))->readOnly(),
                TextInput::make('total_quantity')->label('Jumlah Quantity')->numeric()->required()->rule('numeric'),
                Select::make('currency_id')->relationship('currency', 'currency')->label(trans('Mata Uang'))->preload()->required(),
                TextInput::make('item_amount')->label('Nilai Barang')->numeric()->required()->rule('numeric'),
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


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_date')->sortable()->searchable()->toggleable(),
                TextColumn::make('sj_number')->sortable()->searchable()->toggleable(),
                TextColumn::make('sj_date')->sortable()->searchable()->toggleable(),
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
                    // Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('export')
                    ->label('Export to Excel')
                    ->action(fn () => Excel::download(new HpoutExport, 'Hpout.xlsx'))
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
        return [
            //
        ];
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
