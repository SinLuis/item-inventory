<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'bbin_num',
        'bbin_seri',
        'item_id',
        'item_description',
        'item_uofm',
        'total_quantity',
        'item_amount',
        // Add other attributes here
    ];
}
