<?php

namespace App\Filament\Resources\WasteResource\Pages;

use App\Filament\Resources\WasteResource;
use App\Models\Bbout;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateWaste extends CreateRecord
{
    protected static string $resource = WasteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // protected function beforeCreate(): void
    // {   
    //     $data = $this->data;
    //     // dd($data['bbin']);
    //     // $hpin = Hpin::find($data['id']);
    //     $bbout = Bbout::find($data['bbout_id']);
    //     if ($data['total_quantity'] > $bbout->quantity_remaining) {
    //         Notification::make()
    //         ->title('The Maximum Quantity is ' . $bbout->quantity_remaining)
    //         ->danger() // Use danger() for error notifications
    //         ->send();
    //         $this->halt();
    //     }
    //     if ($data['document_date'] < $bbout->document_date ){
    //         Notification::make()
    //         ->title('Waste Document Date cannot Older than ' . $bbout->document_date)
    //         ->danger() // Use danger() for error notifications
    //         ->send();
    //         $this->halt();
    //     }
    //     else{
    //         // $bbout->quantity_remaining = $data['qty_after'];
    //         $bbout->quantity_remaining = $bbout->quantity_remaining - $data['total_quantity'] ;
    //         $bbout->save();
    //     }


    // }
}
