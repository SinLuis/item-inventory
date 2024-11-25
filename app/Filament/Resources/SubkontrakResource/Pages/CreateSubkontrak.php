<?php

namespace App\Filament\Resources\SubkontrakResource\Pages;

use App\Filament\Resources\SubkontrakResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubkontrak extends CreateRecord
{
    protected static string $resource = SubkontrakResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
