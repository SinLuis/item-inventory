<?php

namespace App\Filament\Resources\UofmResource\Pages;

use App\Filament\Resources\UofmResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUofm extends EditRecord
{
    protected static string $resource = UofmResource::class;

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
