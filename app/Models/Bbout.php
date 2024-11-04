<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bbout extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'bbin',
        'bbin_num',
        'bbin_seri',
        'item_id',
        'item_description',
        'item_uofm',
        'subkontrak_name',
        'subkontrak_id',
        'use_quantity',
        'sub_quantity',
        'subkontrak_id',
        'notes',
        'fg_id',
        'fg_description',
        'user_id',
        'user_name'
        // Add other attributes here
    ];
    
    public function bbin()
    {
        return $this->belongsTo(Bbin::class);
    }

    public function subsupplier()
    {
        return $this->belongsTo(Supplier::class, 'subkontrak_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'fg_id');
    }

    
    public function setQtyAttribute($value)
    {
        $this->attributes['sub_quantity'] = $value !== null ? $value : '0';
    }

    public function hpin()
    {
        return $this->hasMany(Hpin::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

  

}
