<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use HasFactory;

    protected $fillable = [
        'storage'
        // Add other attributes here
    ];

    public function bbin()
    {
        return $this->hasMany(Bbin::class);
    }

    public function hpin()
    {
        return $this->hasMany(Hpin::class);
    }

    public function mutationout()
    {
        return $this->hasMany(MutationOut::class);
    }

    public function stockcard()
    {
        return $this->hasMany(Stockcard::class);
    }


    protected $table = 'storages';
}
