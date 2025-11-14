<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wip extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'hp_id',
        'pib_number',
        'seri_number',
        'wip_id',
        'wip_code',
        'wip_description',
        'wip_uofm',
        'wip_quantity',
        'wip_quantity_remaining',
        'fg_id',
        'fg_code',
        'fg_description',
        'fg_uofm_id',
        'fg_uofm_description',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    protected static function booted()
    {

        parent::boot();


        static::saving(function ($wip) {
            if (is_null($wip->wip_quantity_remaining)) {
                $wip->wip_quantity_remaining = ($wip->wip_quantity ?? 0);
            }

        });

    }


    public function item()
    {
        return $this->belongsTo(Item::class, 'fg_id');
    }

    public function hpin()
    {
        return $this->belongsTo(Hpin::class);
    }
}


    