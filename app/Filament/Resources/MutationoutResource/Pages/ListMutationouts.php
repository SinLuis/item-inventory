<?php

namespace App\Filament\Resources\MutationoutResource\Pages;

use App\Filament\Resources\MutationoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMutationouts extends ListRecords
{
    protected static string $resource = MutationoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
