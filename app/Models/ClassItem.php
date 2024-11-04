<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassItem extends Model
{
    use HasFactory;
    
    protected $table = 'class_items';

    protected $fillable = [
        'code',
        'description'
        // Add other attributes here
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

   
}
