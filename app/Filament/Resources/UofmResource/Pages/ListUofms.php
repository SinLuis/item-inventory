<?php

namespace App\Filament\Resources\UofmResource\Pages;

use App\Filament\Resources\UofmResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUofms extends ListRecords
{
    protected static string $resource = UofmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
