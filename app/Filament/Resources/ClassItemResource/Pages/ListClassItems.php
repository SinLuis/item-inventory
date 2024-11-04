<?php

namespace App\Filament\Resources\ClassItemResource\Pages;

use App\Filament\Resources\ClassItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassItems extends ListRecords
{
    protected static string $resource = ClassItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array 
    {
        return [5, 15, 25, 50, -1];
    }
}
