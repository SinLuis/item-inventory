<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subkontrak extends Model
{
    use HasFactory;

    protected $fillable = [
        'reff_number',
        'reff_date',
        'item_id',
        'item_description',
        'item_uofm',
        'pib_number',
        'seri_number',
        'total_quantity',
        'subkontrak_id',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'subkontrak_id');
    }
}
