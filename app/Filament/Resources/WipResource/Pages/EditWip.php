<?php

namespace App\Filament\Resources\WipResource\Pages;

use App\Filament\Resources\WipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWip extends EditRecord
{
    protected static string $resource = WipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
