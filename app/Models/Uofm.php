<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uofm extends Model
{
    use HasFactory;

    protected $table = 'uofms';

    protected $fillable = [
        'code',
        'description'
        // Add other attributes here
    ];
    public function item()
    {
        return $this->hasMany(Item::class);
    }

    

}
