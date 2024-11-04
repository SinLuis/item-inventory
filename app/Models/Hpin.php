<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hpin extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'bbout',
        'item_id',
        'item_description',
        'item_uofm',
        'bbin_num',
        'seri_num',
        'produce_quantity',
        'sub_quantity',
        'storages_id',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    public function bbin()
    {
        return $this->belongsTo(Bbin::class);
    }

    public function hpout()
    {
        return $this->hasMany(Hpout::class);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class,'storages_id');
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
