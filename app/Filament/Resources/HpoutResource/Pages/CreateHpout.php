<?php

namespace App\Filament\Resources\HpoutResource\Pages;

use App\Filament\Resources\HpoutResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHpout extends CreateRecord
{
    protected static string $resource = HpoutResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
