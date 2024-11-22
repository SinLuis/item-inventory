<?php

namespace App\Filament\Resources\BbinadjResource\Pages;

use App\Filament\Resources\BbinadjResource;
use App\Models\Bbin;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;


class CreateBbinadj extends CreateRecord
{
    protected static string $resource = BbinadjResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {   
        $data = $this->data;
        // dd($data['bbin']);
        $bbin = Bbin::find($data['bbin_id']);
        if ($data['qty_after'] < ($bbin->total_quantity - $bbin->quantity_remaining)) {
            Notification::make()
            ->title('The Quantity After must be at least ' . $bbin->total_quantity - $bbin->quantity_remaining)
            ->danger() // Use danger() for error notifications
            ->send();
            $this->halt();
        }else{
            $bbin->total_quantity = $data['qty_after'];
            $bbin->quantity_remaining = $bbin->quantity_remaining + ($data['qty_after'] - $data['qty_before']);
            $bbin->save();
        }


    }



}
