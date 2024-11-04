<?php

namespace App\Filament\Resources\HpoutResource\Pages;

use App\Filament\Resources\HpoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHpout extends EditRecord
{
    protected static string $resource = HpoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
