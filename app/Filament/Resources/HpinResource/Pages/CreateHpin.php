<?php

namespace App\Filament\Resources\HpinResource\Pages;

use App\Filament\Resources\HpinResource;
use App\Models\Bbout;
use App\Models\Hpin;
use App\Models\Waste;
use App\Models\Wip;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateHpin extends CreateRecord
{
    protected static string $resource = HpinResource::class;
    
    protected function handleRecordCreation(array $data): Model
    {
        $rows = $data['rows'] ?? [];
        $last = null;

        foreach ($rows as $row) {
            $row['user_id']   = auth()->id();
            $row['user_name'] = auth()->user()->name;
            $last = Hpin::create($row);
        }

        // If no rows were submitted, create a minimal record or throw validation as you prefer.
        if (! $last) {
            $last = Bbin::create([
                'user_id'   => auth()->id(),
                'user_name' => auth()->user()->name,
            ]);
        }

        return $last;
    } 

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
    $rows = $this->data['rows'] ?? [];

    foreach ($rows as $row) {
        $bbout = isset($row['bbout_id']) ? Bbout::find($row['bbout_id']) : null;
        $wip   = isset($row['wips_id']) ? Wip::find($row['wips_id']) : null;

        // Kalau bbout_id != 0, cek pakai BBOUT
        if (!empty($row['bbout_id']) && $row['bbout_id'] != 0 && $bbout) {
            if (($row['fg_quantity'] ?? 0) == 0 && ($row['sub_quantity'] ?? 0) == 0) {
                Notification::make()
                    ->title('Produce Quantity and Sub Quantity can\'t be both 0')
                    ->danger()
                    ->send();
                $this->halt();
            }

            if ($row['document_date'] < $bbout->document_date) {
                Notification::make()
                    ->title('HP IN Document Date cannot be older than ' . $bbout->document_date)
                    ->danger()
                    ->send();
                $this->halt();
            }

            if (
                ($row['fg_quantity'] + $row['sub_quantity'] + $row['wip_quantity'])
                > $bbout->quantity_remaining
            ) {
                Notification::make()
                    ->title('The Maximum Quantity is ' . $bbout->quantity_remaining)
                    ->danger()
                    ->send();
                $this->halt();
            } else {
                // Update quantity_remaining
                $bbout->quantity_remaining = 0; 
                // max(
                //     0,
                //     $bbout->quantity_remaining - ($row['fg_quantity'] + $row['sub_quantity'] + $row['wip_quantity'])
                // );
                $bbout->save();

                // if($bbout->quantity_remaining != 0){
                //     Waste::create([
                //         'document_number' => $row['document_number'],
                //         'document_date' => $row['document_date'],
                //         'pib_number' => $bbout->pib_number,
                //         'seri_number' => $bbout->seri_number,
                //         'bbout_id' => $bbout->id,
                //         'item_id' => 25,
                //         'item_code' => 'WST-001',
                //         'item_description' => 'WASTE',
                //         'item_uofm' => 'KG',
                //         'total_quantity' => $bbout->quantity_remaining,
                //         'item_amount' => 0,
                //         'user_id' => auth()->id(),
                //         'user_name' => auth()->user()->name,
                //     ]);
                //     $bbout->quantity_remaining = 0;
                // }
            }
        }

        // Kalau pakai WIP
        elseif (!empty($row['wips_id']) && $row['wips_id'] != 0 && $wip) {
            if (($row['fg_quantity'] ?? 0) == 0 && ($row['sub_quantity'] ?? 0) == 0) {
                Notification::make()
                    ->title('Produce Quantity and Sub Quantity can\'t be both 0')
                    ->danger()
                    ->send();
                $this->halt();
            }

            if ($row['document_date'] < $wip->document_date) {
                Notification::make()
                    ->title('HP IN Document Date cannot be older than ' . $wip->document_date)
                    ->danger()
                    ->send();
                $this->halt();
            }

            if (
                ($row['fg_quantity'] + $row['sub_quantity'] + $row['wip_quantity'])
                > $wip->wip_quantity_remaining
            ) {
                Notification::make()
                    ->title('The Maximum Quantity is ' . $wip->wip_quantity_remaining)
                    ->danger()
                    ->send();
                $this->halt();
            } else {
                // Update sisa WIP
                $wip->wip_quantity_remaining -= ($row['fg_quantity'] + $row['sub_quantity'] + $row['wip_quantity']);
                $wip->save();
            }
        }
    }
}

}
