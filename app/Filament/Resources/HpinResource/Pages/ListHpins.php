<?php

namespace App\Filament\Resources\HpinResource\Pages;

use App\Filament\Resources\HpinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHpins extends ListRecords
{
    protected static string $resource = HpinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
