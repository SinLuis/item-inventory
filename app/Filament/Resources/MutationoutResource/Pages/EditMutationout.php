<?php

namespace App\Filament\Resources\MutationoutResource\Pages;

use App\Filament\Resources\MutationoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMutationout extends EditRecord
{
    protected static string $resource = MutationoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
