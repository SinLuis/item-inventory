<?php

namespace App\Filament\Resources\BboutResource\Pages;

use App\Filament\Resources\BboutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBbout extends EditRecord
{
    protected static string $resource = BboutResource::class;

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
