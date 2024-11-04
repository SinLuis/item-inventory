<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public function bbin()
    {
        return $this->hasMany(Bbin::class);
    }

    public function hpout()
    {
        return $this->hasMany(Hpout::class);
    }

    protected $table = 'currencies';
}
