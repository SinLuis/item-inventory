<?php

namespace App\Filament\Resources\BbinResource\Pages;

use App\Filament\Resources\BbinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBbins extends ListRecords
{
    protected static string $resource = BbinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
