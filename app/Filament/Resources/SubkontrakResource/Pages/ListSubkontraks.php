<?php

namespace App\Filament\Resources\SubkontrakResource\Pages;

use App\Filament\Resources\SubkontrakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubkontraks extends ListRecords
{
    protected static string $resource = SubkontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
