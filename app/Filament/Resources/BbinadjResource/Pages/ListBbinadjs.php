<?php

namespace App\Filament\Resources\BbinadjResource\Pages;

use App\Filament\Resources\BbinadjResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBbinadjs extends ListRecords
{
    protected static string $resource = BbinadjResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
