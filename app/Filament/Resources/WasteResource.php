<?php

namespace App\Filament\Resources;

use App\Exports\WasteExport;
use App\Filament\Resources\WasteResource\Pages;
use App\Filament\Resources\WasteResource\RelationManagers;
use App\Models\Waste;
use App\Models\Hpin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
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

class WasteResource extends Resource
{
    protected static ?string $model = Waste::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Storages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('document_number')->label(trans('Nomor BC24'))->required(),
                DatePicker::make('document_date')->label(trans('Tanggal BC24'))->native(false)->required(),

                Select::make('hpin') 
                        ->label(trans('Daftar WIP'))
                        ->options(function () {
                              return Hpin::query()
                                ->get()
                                ->mapWithKeys(function ($hpin) {
                                    if($hpin != null){
                                        return [
                                            $hpin->id => 'PIB: ' . $hpin->bbin_num . ' No Seri: ' . $hpin->seri_num . ' - Kode: ' . $hpin->item_id . ' ' . $hpin->item_description . ' - Produksi: ' . $hpin->produce_quantity . ' ' . $hpin->item_uofm 
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
                            $hpin = Hpin::find($state); 
                           
                            if ($hpin) {
                                $set('bbin_num', $hpin->bbin_num);
                                $set('bbin_seri', $hpin->seri_num);
                                $set('item_id', $hpin->item_id);
                                $set('item_description', $hpin->item_description);
                                $set('item_uofm', $hpin->item_uofm);

                            } else {
                                $set('bbin_num', null);
                                $set('bbin_seri', null);
                                $set('item_id', null);
                                $set('item_description', null);
                                $set('item_uofm', null);
           
                            }
                            
                        }),

                        TextInput::make('bbin_num')->label(trans('PIB'))->readOnly()->required(),
                        TextInput::make('bbin_seri')->label(trans('No Seri'))->readOnly(),
                        TextInput::make('item_id')->label(trans('Kode Barang'))->readOnly(),
                        TextInput::make('item_description')->label(trans('Nama Barang'))->readOnly(),
                        TextInput::make('item_uofm')->label(trans('Satuan'))->readOnly(),
                        TextInput::make('total_quantity')->label('Jumlah')->numeric()->required()->rule('numeric'),
                        TextInput::make('item_amount')->label('Nilai Barang')->numeric()->required()->rule('numeric'),
            ]);
    }

    public static function getLabel(): string
    {
        return 'Waste';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bbin_num')->label('PIB')->sortable()->searchable()->toggleable(),
                TextColumn::make('bbin_seri')->label('Seri Number')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_number')->label('Nomor BC24')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_date')->label('Tanggal BC24')->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('export')
                    ->label('Export to Excel')
                    ->action(fn () => Excel::download(new WasteExport, 'Waste.xlsx'))
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
            'index' => Pages\ListWastes::route('/'),
            'create' => Pages\CreateWaste::route('/create'),
            'edit' => Pages\EditWaste::route('/{record}/edit'),
        ];
    }
}
