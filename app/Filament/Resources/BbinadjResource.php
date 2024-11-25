<?php

namespace App\Filament\Resources;

use App\Exports\BbinadjExport;
use App\Filament\Resources\BbinadjResource\Pages;
use App\Filament\Resources\BbinadjResource\RelationManagers;
use App\Models\Bbinadj;
use App\Models\Bbin;
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
                Hidden::make('document_id'),
                TextInput::make('document_code')->label(trans('Jenis Dokumen'))->readOnly(),
                TextInput::make('document_date')->label(trans('Tanggal'))->readOnly(),
                // 1 Open

                Select::make('bbin_id') 
                        ->label(trans('Daftar BB Masuk'))
                        ->options(function () {
                              return Bbin::query()
                                ->get()
                                ->mapWithKeys(function ($bbin) {
                                    if($bbin != null){
                                        return [
                                            $bbin->id => 'PIB: ' . $bbin->document_number . ', No Seri: ' . $bbin->seri_number . ', ' . $bbin->item->description . ', Quantity Remaining : ' . $bbin->quantity_remaining . ' ' . $bbin->item->uofm->code . ' - Gudang: ' . $bbin->storage->storage
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
                                $set('document_id', $bbin->document_id);
                                $set('document_code', $bbin->document->code);
                                $set('document_date', $bbin->document_date);
                                $set('pib_number', $bbin->document_number);
                                $set('seri_number', $bbin->seri_number);
                                $set('qty_before', $bbin->total_quantity);
                                
                                
                            } else {
                                $set('document_id', null);
                                $set('document_description', null);
                                $set('document_date', null);
                                $set('pib_number', null);
                                $set('seri_number', null);
                                $set('qty_before', null);
                            }
                           
                        }),

                // 1 Close
                
                TextInput::make('pib_number')->label(trans('No Dok PIB'))->readOnly(),
                TextInput::make('seri_number')->label(trans('Seri Barang'))->readOnly(),
                TextInput::make('qty_before')->label(trans('Qty Before'))->readOnly(),
                TextInput::make('qty_after')->label(trans('Qty After'))->rule('min:0'),
                TextInput::make('notes')->label(trans('Remark')),
                DatePicker::make('adjust_date')->label(trans('Tanggal Penyesuaian'))->native(false)->required(),
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
                TextColumn::make('document_code')->label('Jenis Dokumen')->sortable()->searchable()->toggleable(),
                TextColumn::make('document_date')->label('Tanggal')->sortable()->searchable()->toggleable(),
                TextColumn::make('bbin.document_number')->label('No Dok')->sortable()->searchable()->toggleable(),
                TextColumn::make('seri_number')->label('Seri Barang')->sortable()->searchable()->toggleable(),
                TextColumn::make('qty_before')->label('Qty Lama')->sortable()->searchable()->toggleable(),
                TextColumn::make('qty_after')->label('Qty Baru')->sortable()->searchable()->toggleable(),
                TextColumn::make('notes')->label('Remark')->sortable()->searchable()->toggleable(),
                TextColumn::make('adjust_date')->label('Tanggal Penyesuaian')->sortable()->searchable()->toggleable(),
                TextColumn::make('user_name')->label('PIC')->sortable()->searchable()->toggleable(),    
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
                            $query->whereBetween('adjust_date', [
                                $data['start_date'],
                                $data['end_date'],
                            ]);
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
                        ->action(function ($records) {
                            $recordIds = $records->pluck('id')->toArray(); // Extract only the IDs
                            return Excel::download(new BbinadjExport($recordIds), 'Bahan Baku Adjustment.xlsx');
                        })
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
            'index' => Pages\ListBbinadjs::route('/'),
            'create' => Pages\CreateBbinadj::route('/create'),
            'edit' => Pages\EditBbinadj::route('/{record}/edit'),
        ];
    }
}
