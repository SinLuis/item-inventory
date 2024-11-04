<?php

namespace App\Filament\Resources\MutationinResource\Pages;

use App\Filament\Resources\MutationinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMutationins extends ListRecords
{
    protected static string $resource = MutationinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
