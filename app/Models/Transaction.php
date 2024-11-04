<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'type_transaction',
        'document_id',
        'document_number',
        'document_date',
        'seri_number',
        'reff_number',
        'reff_date',
        'item_id',
        'description',
        'total_container',
        'total_quantity',
        'currency_id',
        'item_amount',
        'storages_id',
        'subkontrak_id',
        'supplier_id',
        'country_id'
        // Add other attributes here
    ];
}
