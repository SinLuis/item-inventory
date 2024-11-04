<?php

namespace App\Filament\Resources\BbinResource\Pages;

use App\Filament\Resources\BbinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBbin extends EditRecord
{
    protected static string $resource = BbinResource::class;

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
