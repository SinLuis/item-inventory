<?php

namespace App\Filament\Resources\HpinResource\Pages;

use App\Filament\Resources\HpinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHpin extends EditRecord
{
    protected static string $resource = HpinResource::class;

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
