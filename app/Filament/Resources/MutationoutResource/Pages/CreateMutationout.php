<?php

namespace App\Filament\Resources\MutationoutResource\Pages;

use App\Filament\Resources\MutationoutResource;
use App\Models\Bbin;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMutationout extends CreateRecord
{
    protected static string $resource = MutationoutResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {   
        $data = $this->data;
        // dd($data['bbin']);
        $bbin = Bbin::find($data['bbin_id']);
        if ($data['move_quantity'] > $bbin->quantity_remaining) {
            Notification::make()
            ->title('The Quantity After must be at least ' . $bbin->quantity_remaining)
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }
        if ($data['storagesout_id'] == $data['storagesin_id']) {
            Notification::make()
            ->title('Storage Out and In Cannot be same')
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }
        if ($data['document_date'] < $bbin->document_date ){
            Notification::make()
            ->title('Mutation Out Document Date cannot Older than ' . $bbin->document_date)
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }
        else{
            $bbin->quantity_remaining = $bbin->quantity_remaining - $data['move_quantity'];
            $bbin->save();
        }


    }
}

