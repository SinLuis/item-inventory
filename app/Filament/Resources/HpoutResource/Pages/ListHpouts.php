<?php

namespace App\Filament\Resources\HpoutResource\Pages;

use App\Filament\Resources\HpoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHpouts extends ListRecords
{
    protected static string $resource = HpoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
