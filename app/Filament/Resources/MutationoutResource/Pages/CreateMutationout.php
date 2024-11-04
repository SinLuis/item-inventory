<?php

namespace App\Filament\Resources\MutationoutResource\Pages;

use App\Filament\Resources\MutationoutResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMutationout extends CreateRecord
{
    protected static string $resource = MutationoutResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

