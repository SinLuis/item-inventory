<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_name',
        'address',
        'phone',
        'email',
        'pic'
        // Add other attributes here
    ];

    public function hpout()
    {
        return $this->hasMany(Hpout::class);
    }

    protected $table = 'customers';
}
