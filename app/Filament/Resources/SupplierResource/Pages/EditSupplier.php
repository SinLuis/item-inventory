<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\Rule;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $this->validate([
    //         'supplier_name' => [
    //             'required',
    //             Rule::unique('suppliers')->ignore($this->record->id)->where(function ($query) use ($data) {
    //                 return $query->where('supplier_name', $data['supplier_name'])
    //                              ->where('class_id', $data['class_id']);
    //             })
    //         ],
    //     ]);

    //     return $data;
    // }
}
