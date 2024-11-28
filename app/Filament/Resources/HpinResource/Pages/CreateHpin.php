<?php

namespace App\Filament\Resources\HpinResource\Pages;

use App\Filament\Resources\HpinResource;
use App\Models\Bbout;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateHpin extends CreateRecord
{
    protected static string $resource = HpinResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {   
        $data = $this->data;
        // dd($data['bbin']);
        $bbout = Bbout::find($data['bbout_id']);
        if ($data['produce_quantity'] == 0 && $data['sub_quantity'] == 0){
            Notification::make()
            ->title('Produce Quantity and Sub Quantity can\'t be both 0')
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }
        if ($data['document_date'] < $bbout->document_date ){
            Notification::make()
            ->title('HP IN Document Date cannot Older than ' . $bbout->document_date)
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }
        if ($data['produce_quantity'] + $data['sub_quantity'] > $bbout->quantity_remaining) {
            Notification::make()
            ->title('The Maximum Quantity is ' . $bbout->quantity_remaining)
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }else{
            $bbout->quantity_remaining = $bbout->quantity_remaining - ($data['produce_quantity'] + $data['sub_quantity']);
            $bbout->save();
        }


    }
}
