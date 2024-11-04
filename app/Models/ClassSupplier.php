<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSupplier extends Model
{
    use HasFactory;

    protected $table = 'class_suppliers';
    
    protected $fillable = [
        // 'class_supplier_id',
        'class_supplier_description'
        // Add other attributes here
    ];

    public function supplier()
    {
        return $this->hasMany(Supplier::class);
    }


    
}
