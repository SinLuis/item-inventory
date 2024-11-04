<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bbinadj extends Model
{
    use HasFactory;

    protected $fillable = [
        'bbin_id',
        'qty_before',
        'qty_after',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    public function bbin()
    {
        return $this->belongsTo(Bbin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
