<?php

namespace App\Filament\Resources\BbinResource\Pages;

use App\Filament\Resources\BbinResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBbin extends CreateRecord
{
    protected static string $resource = BbinResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    // protected function beforeCreate(): void
    // {   
    //     $data = $this->data;
    //     // dd($data['bbin']);
    //     // $bbin = Bbin::find($data['bbin_id']);
    //     if ($data['total_quantity'] <= 0) {
    //         Notification::make()
    //         ->title('The Quantity must be at least more than 0')
    //         ->danger() // Use danger() for error notifications
    //         ->send();
    //         $this->halt();
    //     }
    //     if ($data['kurs'] <= 1) {
    //         Notification::make()
    //         ->title('The Kurs must be at least more than 0')
    //         ->danger() // Use danger() for error notifications
    //         ->send();
    //         $this->halt();
    //     }
    //     else{
    
    //         // $data->save();
    //     }
    // }
}