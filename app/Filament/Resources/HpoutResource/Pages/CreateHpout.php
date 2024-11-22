<?php

namespace App\Filament\Resources\HpoutResource\Pages;

use App\Filament\Resources\HpoutResource;
use App\Models\Hpin;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateHpout extends CreateRecord
{
    protected static string $resource = HpoutResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {   
        $data = $this->data;
        // dd($data['bbin']);
        $hpin = Hpin::find($data['hpin_id']);
        if ($data['total_quantity'] > $hpin->quantity_remaining) {
            Notification::make()
            ->title('The Quantity After must be at least ' . $hpin->quantity_remaining)
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }else{
            $hpin->quantity_remaining = $hpin->quantity_remaining - $data['total_quantity'];
            $hpin->save();
        }
    }

}
