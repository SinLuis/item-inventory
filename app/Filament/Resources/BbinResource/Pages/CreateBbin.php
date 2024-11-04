<?php

namespace App\Filament\Resources\BbinResource\Pages;

use App\Filament\Resources\BbinResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBbin extends CreateRecord
{
    protected static string $resource = BbinResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
