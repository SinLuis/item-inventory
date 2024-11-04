<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bbin extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'document_number',
        'document_date',
        'seri_number',
        'reff_number',
        'reff_date',
        'item_id',
        'item_description',
        'item_longdescription',
        'item_uofm',
        'total_container',
        'total_quantity',
        'currency_id',
        'item_amount',
        'storages_id',
        'subkontrak_id',
        'supplier_id',
        'country_id',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class,'storages_id');
    }

    public function supsupplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function subsupplier()
    {
        return $this->belongsTo(Supplier::class, 'subkontrak_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function bbout()
    {
        return $this->hasMany(Bbout::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bbinadj()
    {
        return $this->hasMany(Bbinadj::class);
    }

    public function mutationout()
    {
        return $this->hasMany(Mutationout::class);
    }

    public function mutationin()
    {
        return $this->hasMany(Mutationin::class);
    }
}
