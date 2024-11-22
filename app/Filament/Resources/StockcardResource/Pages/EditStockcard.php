<?php

namespace App\Filament\Resources\StockcardResource\Pages;

use App\Filament\Resources\StockcardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockcard extends EditRecord
{
    protected static string $resource = StockcardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
