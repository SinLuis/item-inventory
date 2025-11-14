<?php

namespace App\Filament\Resources\HpoutResource\Pages;

use App\Filament\Resources\HpoutResource;
use App\Models\Hpin;
use App\Models\Hpout;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateHpout extends CreateRecord
{
    protected static string $resource = HpoutResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $rows = $data['rows'] ?? [];
        $last = null;

        foreach ($rows as $row) {
            $row['user_id']   = auth()->id();
            $row['user_name'] = auth()->user()->name;
            $last = Hpout::create($row);
        }

        // If no rows were submitted, create a minimal record or throw validation as you prefer.
        if (! $last) {
            $last = Hpout::create([
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
        $hpin = Hpin::find($row['hpin_id'] ?? null);

        if (! $hpin) {
            continue; // skip kalau null
        }

        if (($row['total_quantity'] ?? 0) > $hpin->fg_quantity_remaining) {
            Notification::make()
                ->title('The Maximum Quantity is ' . $hpin->fg_quantity_remaining)
                ->danger()
                ->send();
            $this->halt();
        }

        if (($row['document_date'] ?? null) < $hpin->document_date) {
            Notification::make()
                ->title('HP OUT Document Date cannot Older than ' . $hpin->document_date)
                ->danger()
                ->send();
            $this->halt();
        }

        // Update sisa qty
        $hpin->fg_quantity_remaining -= $row['total_quantity'] ?? 0;
        $hpin->save();
    }
}


}
