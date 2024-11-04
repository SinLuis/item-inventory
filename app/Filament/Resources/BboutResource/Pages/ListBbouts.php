<?php

namespace App\Filament\Resources\BboutResource\Pages;

use App\Filament\Resources\BboutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBbouts extends ListRecords
{
    protected static string $resource = BboutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
