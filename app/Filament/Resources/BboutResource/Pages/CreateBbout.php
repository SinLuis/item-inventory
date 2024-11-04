<?php

namespace App\Filament\Resources\BboutResource\Pages;

use App\Filament\Resources\BboutResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBbout extends CreateRecord
{
    protected static string $resource = BboutResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
