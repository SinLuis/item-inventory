<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hpout extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'sj_number',
        'sj_date',
        'customer_id',
        'country_id',
        'hpin',
        'item_id',
        'item_description',
        'item_longdescription',
        'item_uofm',
        'no_pib',
        'seri_number',
        'total_quantity',
        'currency_id',
        'item_amount',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    public function hpin()
    {
        return $this->belongsTo(Hpin::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
