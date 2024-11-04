<?php

namespace App\Filament\Resources\UofmResource\Pages;

use App\Filament\Resources\UofmResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUofm extends CreateRecord
{
    protected static string $resource = UofmResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
