<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'fg_id',
        'rm_id',
        'coefficient',
        'waste',
        // Add other attributes here
    ];

    public function fgitem()
    {
        return $this->belongsTo(Item::class, 'fg_id');
    }

    public function rmitem()
    {
        return $this->belongsTo(Item::class, 'rm_id');
    }
}
