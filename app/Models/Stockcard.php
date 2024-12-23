<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockcard extends Model
{
    use HasFactory;
    protected static ?string $model = Stockcard::class; // This references the Stockcard model
    protected $fillable = [
        'pib_number',
        'seri_number',
        'document_date',
        'transaction_id',
        'transaction_description',
        'transaction_type',
        'item_id',
        'total_quantity',
        'storages_id',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function storages()
    {
        return $this->belongsTo(Storage::class);
    }
}
