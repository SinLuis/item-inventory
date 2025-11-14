<?php

namespace App\Filament\Resources\BboutResource\Pages;

use App\Filament\Resources\BboutResource;
use App\Models\Bbin;
use App\Models\Bbout;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBbout extends CreateRecord
{
    protected static string $resource = BboutResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $rows = $data['rows'] ?? [];
        $last = null;

        foreach ($rows as $row) {
            $row['user_id']   = auth()->id();
            $row['user_name'] = auth()->user()->name;
            $last = Bbout::create($row);
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
        $bbin = Bbin::find($row['bbin_id']);

        if (! $bbin) {
            Notification::make()
                ->title('BBIN tidak ditemukan.')
                ->danger()
                ->send();
            $this->halt();
        }

        if (($row['use_quantity'] ?? 0) == 0 && ($row['sub_quantity'] ?? 0) == 0) {
            Notification::make()
                ->title('Use Quantity dan Sub Quantity tidak boleh keduanya 0')
                ->danger()
                ->send();
            $this->halt();
        }

        if ($row['document_date'] < $bbin->document_date) {
            Notification::make()
                ->title('Tanggal dokumen BB OUT tidak boleh lebih lama dari ' . $bbin->document_date)
                ->danger()
                ->send();
            $this->halt();
        }

        if (($row['use_quantity'] + $row['sub_quantity']) > $bbin->quantity_remaining) {
            Notification::make()
                ->title('Maksimal Quantity adalah ' . $bbin->quantity_remaining)
                ->danger()
                ->send();
            $this->halt();
        } else {
            $bbin->quantity_remaining -= ($row['sub_quantity'] + $row['use_quantity']);
            $bbin->save();
        }
    }
}


    
}
