<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $this->validate([
    //         'supplier_name' => [
    //             'required',
    //             Rule::unique('suppliers')->where(function ($query) use ($data) {
    //                 return $query->where('supplier_name', $data['supplier_name'])
    //                              ->where('class_id', $data['class_id']);
    //             })
    //         ],
    //     ]);

    //     return $data;
    // }
}
