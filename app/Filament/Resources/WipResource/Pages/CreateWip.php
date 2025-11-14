<?php

namespace App\Filament\Resources\WipResource\Pages;

use App\Filament\Resources\WipResource;
use App\Models\Hpin;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateWip extends CreateRecord
{
    protected static string $resource = WipResource::class;

        protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {   
        $data = $this->data;
        // dd($data);
        // dd($data['bbin']);
        $hpin = Hpin::find($data['hp_id']);
        if ($data['wip_quantity'] == 0){
            Notification::make()
            ->title('WIP Quantity  can\'t be 0')
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }
        if ($data['document_date'] < $hpin->document_date ){
            Notification::make()
            ->title('WIP Document Date cannot Older than ' . $hpin->document_date)
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }
        if ($data['wip_quantity'] > $hpin->wip_quantity_remaining) {
            Notification::make()
            ->title('The Maximum Quantity is ' . $hpin->wip_quantity_remaining)
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }else{
            // $bbout->quantity_remaining = $bbout->quantity_remaining - ($data['fg_quantity'] + $data['sub_quantity'] + $data['wip_quantity']);
            $hpin->wip_quantity_remaining = $hpin->wip_quantity_remaining - $data['wip_quantity'];
            $data['wip_quantit_remaining'] = $data['wip_quantity'];
            $hpin->save();
        }


    }
}
