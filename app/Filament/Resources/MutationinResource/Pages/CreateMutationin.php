<?php

namespace App\Filament\Resources\MutationinResource\Pages;

use App\Filament\Resources\MutationinResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMutationin extends CreateRecord
{
    protected static string $resource = MutationinResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
