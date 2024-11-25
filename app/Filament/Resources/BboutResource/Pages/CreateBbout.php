<?php

namespace App\Filament\Resources\BboutResource\Pages;

use App\Filament\Resources\BboutResource;
use App\Models\Bbin;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBbout extends CreateRecord
{
    protected static string $resource = BboutResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function beforeCreate(): void
    {   
        $data = $this->data;
        // dd($data['bbin']);
        $bbin = Bbin::find($data['bbin_id']);
        if ($data['use_quantity'] == 0 && $data['sub_quantity'] == 0){
            Notification::make()
            ->title('Use Quantity and Sub Quantity can\'t be both 0')
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }
        if ($data['use_quantity'] + $data['sub_quantity'] > $bbin->quantity_remaining) {
            Notification::make()
            ->title('The Maximum Quantity is ' . $bbin->quantity_remaining)
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }else{
            $bbin->quantity_remaining = $bbin->quantity_remaining - ($data['sub_quantity'] + $data['use_quantity']);
            // $data['quantity_remaining'] = $data['sub_quantity'] + $data['use_quantity'];
            $bbin->save();
        }


    }

    
}
