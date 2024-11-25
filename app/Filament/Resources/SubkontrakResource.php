<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubkontrakResource\Pages;
use App\Filament\Resources\SubkontrakResource\RelationManagers;
use App\Models\Subkontrak;
use App\Models\Item;
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

class SubkontrakResource extends Resource
{
    protected static ?string $model = Subkontrak::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Transaction';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('reff_number')->label(trans('Nomor Surat'))->required(),
                DatePicker::make('reff_date')->label(trans('Tanggal Surat'))->native(false)->closeonDateSelection()->required(),
                Select::make('item_id')->relationship('item', 'code', function(Builder $query){
                    return $query->where('class_id', 1);
                })->label(trans('Item ID'))->required()->searchable()->preload()->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    $item = Item::find($state);
                    if ($item) {
                        $set('item_description', $item->description);
                        $set('item_longdescription', $item->long_description);
                        $set('item_uofm', $item->uofm->code);
                    } else {
                        $set('item_description', null);
                        $set('item_longdescription', null);
                        $set('item_uofm', null);
                    }
                }),
                TextInput::make('item_description')->label(trans('Nama Item'))->readOnly(),
                TextInput::make('item_uofm')->label(trans('Satuan Item'))->readOnly(),
                TextInput::make('pib_number')->label(trans('No PIB'))->required(),
                TextInput::make('seri_number')->label(trans('No Seri'))->required(),
                TextInput::make('total_quantity')->label('Jumlah Disubkontrakkan')->numeric()->required()->rule('numeric')->rule('gt:0'),
                Select::make('subkontrak_id')->relationship('supplier', 'supplier_name', function(Builder $query){
                    return $query->where('class_id', 2);
                })->label(trans('Penerima Subkontrak'))->preload()->searchable(),
                Hidden::make('user_id')->default(auth()->id()),
                TextInput::make('user_name')
                ->label(trans('User'))
                ->default(auth()->user()->name) // Mengatur nilai default menjadi nama pengguna saat ini
                ->readOnly()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reff_number')->label('Nomor Surat')->sortable()->searchable()->toggleable(),
                TextColumn::make('pib_number')->sortable()->searchable()->toggleable(),
                TextColumn::make('seri_number')->sortable()->searchable()->toggleable(),
                TextColumn::make('reff_date')->label('Tanggal Surat')->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSubkontraks::route('/'),
            'create' => Pages\CreateSubkontrak::route('/create'),
            'edit' => Pages\EditSubkontrak::route('/{record}/edit'),
        ];
    }
}
