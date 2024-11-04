<?php

namespace App\Filament\Resources\ClassItemResource\Pages;

use App\Filament\Resources\ClassItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClassItem extends CreateRecord
{
    protected static string $resource = ClassItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
