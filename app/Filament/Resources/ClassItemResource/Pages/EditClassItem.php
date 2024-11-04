<?php

namespace App\Filament\Resources\ClassItemResource\Pages;

use App\Filament\Resources\ClassItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassItem extends EditRecord
{
    protected static string $resource = ClassItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
