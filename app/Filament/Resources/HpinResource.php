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
                TextInput::make('document_number')->label(trans('Nomor Surat'))->required(),
                DatePicker::make('document_date')->label(trans('Tanggal Surat'))->native(false)->required(),
                
                Select::make('bbout') 
                        ->label(trans('Daftar WIP'))
                        ->options(function () {
                              return Bbout::query()
                                ->get()
                                ->mapWithKeys(function ($bbout) {
                                    if($bbout != null){
                                        return [
                                            $bbout->id => 'PIB: ' . $bbout->bbin_num . ', No Seri: ' . $bbout->bbin_seri . ', ' . $bbout->fg_description . ', Jumlah: ' . $bbout->use_quantity . ' ' . $bbout->item_uofm 
                                        ]; 
                                    }
                                    else{
                                        return null;
                                    }
                                    
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->reactive()->afterStateUpdated(function (callable $set, $state) {
                            $bbout = Bbout::find($state); 
                            
                            if ($bbout) {
                                $set('item_id', $bbout->item->code);
                                $set('item_description', $bbout->fg_description);
                                $set('item_uofm', $bbout->item_uofm);
                                $set('bbin_num', $bbout->bbin_num);
                                $set('seri_num', $bbout->bbin_seri);
                                // $set('produce_quantity', $bbout->use_quantity);
                                $set('sub_quantity', $bbout->sub_quantity);
                                
                            
                            } else {
                                $set('item_id', null);
                                $set('item_description', null);
                                $set('item_uofm', null);
                                $set('bbin_num', null);
                                $set('seri_num', null);
                                // $set('produce_quantity', null);
                                $set('sub_quantity', null);
                            }
                            
                        }),
                        TextInput::make('item_id')->label(trans('Kode Barang'))->readOnly(),
                        TextInput::make('item_description')->label(trans('Nama Barang'))->readOnly(),
                        TextInput::make('item_uofm')->label(trans('Satuan'))->readOnly(),
                        TextInput::make('bbin_num')->label(trans('No PIB'))->readOnly()->required(),
                        TextInput::make('seri_num')->label(trans('No Seri'))->readOnly(),
                        TextInput::make('produce_quantity')->label(trans('Jumlah dari Produksi')),
                        TextInput::make('sub_quantity')->label(trans('Jumlah dari Subkontrak'))->readOnly(),
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
                    // Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('export')
                    ->label('Export to Excel')
                    ->action(fn () => Excel::download(new HpinExport, 'Hpin.xlsx'))
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
