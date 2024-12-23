<?php

namespace App\Filament\Resources\StockcardResource\Pages;

use App\Filament\Resources\StockcardResource;
use App\Models\Stockcard;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListStockcards extends ListRecords
{
    protected static string $resource = StockcardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Stockcard::query()
            ->join('storages', 'storages.id', '=', 'stockcards.storages_id')
            ->orderBy('storage', 'asc')
            ->orderBy('document_date', 'asc') 
            ->select('stockcards.*');
    }
}
