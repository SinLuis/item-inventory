<?php

namespace App\Filament\Resources\MutationinResource\Pages;

use App\Filament\Resources\MutationinResource;
use App\Models\Mutationout;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMutationin extends CreateRecord
{
    protected static string $resource = MutationinResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {   
        $data = $this->data;
        // dd($data['bbin']);
        $mutationout = Mutationout::find($data['mutationout_id']);
        if ($data['document_date'] < $mutationout->document_date ){
            Notification::make()
            ->title('Mutation In Document Date cannot Older than ' . $mutationout->document_date)
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }
    }

}
