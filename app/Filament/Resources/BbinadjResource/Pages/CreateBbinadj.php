<?php

namespace App\Filament\Resources\BbinadjResource\Pages;

use App\Filament\Resources\BbinadjResource;
use App\Models\Bbin;
use Filament\Actions;
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
        // dd($bbin);
        $bbin->total_quantity = $data['qty_after'];
        $bbin->save();
    }
}
