<?php

namespace App\Filament\Resources\MutationinResource\Pages;

use App\Filament\Resources\MutationinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMutationin extends EditRecord
{
    protected static string $resource = MutationinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
