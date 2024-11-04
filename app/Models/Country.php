<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'country'
        // Add other attributes here
    ];

    public function bbin()
    {
        return $this->hasMany(Bbin::class);
    }

    public function hpout()
    {
        return $this->hasMany(Hpout::class);
    }

    protected $table = 'countries';
}
