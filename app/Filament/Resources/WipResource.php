<?php

namespace App\Filament\Resources;

use NumberFormatter;
use App\Exports\HpinExport;
use App\Filament\Resources\WipResource\Pages;
use App\Filament\Resources\WipResource\RelationManagers;
use App\Models\Hpin;
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

class WipResource extends Resource
{
    protected static ?string $model = Wip::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-down';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('document_number')->label(trans('Nomor Invoice'))->required(),
                DatePicker::make('document_date')->label(trans('Tanggal Invoice'))->native(false)->closeonDateSelection()->required(),
                //HP ID

                Select::make('hp_id') 
                        ->label(trans('Daftar WIP'))
                        ->options(function () {
                            $hpin = Hpin::query()->get();

                            Log::info('BBOut Retrieved:', $hpin->toArray());
        
                            if ($hpin->isEmpty()) {
                                return []; 
                            }
        
                            return $hpin->mapWithKeys(function ($hpin) {
                                if ($hpin && $hpin->wip_quantity_remaining > 0) {
                                    return [
                                        $hpin->id => $hpin->bbout->bbin->document->code . ': ' . $hpin->pib_number . 
                                                    ', No Seri: ' . $hpin->seri_number . 
                                                    ', ' . $hpin->wip_code .
                                                    ' ' . $hpin->wip_description . 
                                                    ', Jumlah: ' . $hpin->wip_quantity_remaining . 
                                                    ' ' . $hpin->wip_uofm
                                                    // ' - Gudang: ' . $bbout->storage->storage
                                    ]; 
                                }
        
                                return []; // Return an empty array for invalid bbins
                            })->toArray();
                        })
                        ->searchable()
                        ->reactive()->afterStateUpdated(function (callable $set, $state) {
                            $hpin = Hpin::find($state); 
                            
                            if ($hpin) {
                                $set('wip_id', $hpin->wip_id);
                                $set('wip_code', $hpin->wip_code);
                                $set('wip_description', $hpin->wip_description);
                                $set('wip_uofm', $hpin->wip_uofm);
                                $set('pib_number', $hpin->pib_number);
                                $set('seri_number', $hpin->seri_number);
                                // $set('produce_quantity', $bbout->use_quantity);
                                $set('wip_quantity', $hpin->wip_quantity_remaining);
                                $set('wip_quantity_remaining', $hpin->wip_quantity_remaining);
                                
                            
                            } else {
                                $set('wip_id', null);
                                $set('wip_code', null);
                                $set('wip_description', null);
                                $set('wip_uofm', null);
                                $set('pib_number', null);
                                $set('seri_number', null);
                                // $set('produce_quantity', null);
                                $set('wip_quantity', null);
                            }
                            
                        }),
                        Hidden::make('wip_id'),
                        Hidden::make('wip_code'),
                        TextInput::make('wip_description')->label(trans('Item Deskripsi WIP'))->readOnly(),
                        TextInput::make('wip_uofm')->label(trans('Satuan WIP'))->readOnly(),
                        TextInput::make('wip_quantity')->label(trans('Qty WIP'))->numeric(),
                        Hidden::make('wip_quantity'),
                        TextInput::make('pib_number')->label(trans('No PIB'))->readOnly()->required(),
                        TextInput::make('seri_number')->label(trans('No Seri'))->readOnly(),
                        
                        Select::make('fg_id')->relationship('item', 'code', function(Builder $query){
                        return $query->where('class_id', 2);
                        })->label(trans('Item FG'))->searchable()->preload()->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $item = Item::find($state);
                            if ($item) {
                                $set('fg_id', $item->id);
                                $set('fg_code', $item->code);
                                $set('fg_description', $item->description);
                                $set('fg_uofm_id', $item->uofm->id);
                                $set('fg_uofm_description', $item->uofm->code);
       
                            } else {
                                $set('fg_id', null);
                                $set('fg_code', null);
                                $set('fg_description', null);
                                $set('fg_uofm_id', null);
                                $set('fg_uofm_description', null);
                            }
                        }),

                        Hidden::make('fg_id'),
                        Hidden::make('fg_code'),
                        // TextInput::make('fg_code')->label(trans('Kode FG'))->readOnly(),
                        TextInput::make('fg_description')->label(trans('Nama FG'))->readOnly(),
                        Hidden::make('fg_uofm_id'),
                        TextInput::make('fg_uofm_description')->label(trans('Uofm'))->readOnly(),
                        Hidden::make('user_id')->default(auth()->id()),
                        TextInput::make('user_name')
                        ->label(trans('User'))
                        ->default(auth()->user()->name) // Mengatur nilai default menjadi nama pengguna saat ini
                        ->readOnly(),
            ]);
    }

    public static function getLabel(): string
    {
        return 'WIP';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')->label('Nomor Dokumen')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_date')->label('Tanggal Dokumen')->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWips::route('/'),
            'create' => Pages\CreateWip::route('/create'),
            'edit' => Pages\EditWip::route('/{record}/edit'),
        ];
    }
}
