<?php

namespace App\Filament\Resources\BbinResource\Pages;

use App\Filament\Resources\BbinResource;
use App\Models\Bbin;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBbin extends CreateRecord
{
    protected static string $resource = BbinResource::class;

    /**
     * Create multiple Bbin records from the repeater rows.
     */
    protected function handleRecordCreation(array $data): Model
    {
        $rows = $data['rows'] ?? [];
        $last = null;

        foreach ($rows as $row) {
            $row['user_id']   = auth()->id();
            $row['user_name'] = auth()->user()->name;
            $last = Bbin::create($row);
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

    /**
     * After creating many, go back to index instead of opening the last record.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
