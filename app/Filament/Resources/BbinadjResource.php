<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BbinadjResource\Pages;
use App\Filament\Resources\BbinadjResource\RelationManagers;
use App\Models\Bbinadj;
use App\Models\Bbin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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

class BbinadjResource extends Resource
{
    protected static ?string $model = Bbinadj::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // 1 Open

                Select::make('bbin_id') 
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
                                $set('qty_before', $bbin->total_quantity);
                                
                            } else {
                                $set('qty_before', null);
                            }
                            
                        }),

                // 1 Close

                TextInput::make('qty_before')->label(trans('Qty Before'))->readOnly(),
                TextInput::make('qty_after')->label(trans('Qty After')),
                Hidden::make('user_id')->default(auth()->id()),
                TextInput::make('user_name')
                ->label(trans('User'))
                ->default(auth()->user()->name) // Mengatur nilai default menjadi nama pengguna saat ini
                ->readOnly(),

            ]);
    }

    public static function getLabel(): string
    {
        return 'BB IN ADJUSTMENT';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bbin.document_number')->sortable()->searchable()->toggleable(),
                TextColumn::make('qty_before')->sortable()->searchable()->toggleable(),
                TextColumn::make('qty_after')->sortable()->searchable()->toggleable(),
                TextColumn::make('user_name')->sortable()->searchable()->toggleable()
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
            'index' => Pages\ListBbinadjs::route('/'),
            'create' => Pages\CreateBbinadj::route('/create'),
            'edit' => Pages\EditBbinadj::route('/{record}/edit'),
        ];
    }
}
