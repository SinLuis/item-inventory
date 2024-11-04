<?php

namespace App\Filament\Resources\HpinResource\Pages;

use App\Filament\Resources\HpinResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHpin extends CreateRecord
{
    protected static string $resource = HpinResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
