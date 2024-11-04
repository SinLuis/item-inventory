<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'uofm_id',
        'code',
        'description',
        'long_description',
        'class_id',
        // Add other attributes here
    ];

    public function class()
    {
        return $this->belongsTo(ClassItem::class);
    }

    public function uofm()
    {
        return $this->belongsTo(Uofm::class);
    }

    public function conversion()
    {
        return $this->hasMany(Conversion::class);
    }

    public function bbin()
    {
        return $this->hasMany(Bbin::class);
    }

    public function bbout()
    {
        return $this->hasMany(Bbout::class);
    }

    public function hpout()
    {
        return $this->hasMany(Hpout::class);
    }
    
 
}
