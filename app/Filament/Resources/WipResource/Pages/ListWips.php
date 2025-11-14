<?php

namespace App\Filament\Resources\WipResource\Pages;

use App\Filament\Resources\WipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWips extends ListRecords
{
    protected static string $resource = WipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
