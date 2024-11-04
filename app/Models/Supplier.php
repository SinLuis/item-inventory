<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'class_id',
        'address',
        'phone',
        'email',
        'pic'
        // Add other attributes here
    ];


    public function class()
    {
        return $this->belongsTo(ClassSupplier::class);
    }

    public function bbin()
    {
        return $this->hasMany(Bbin::class);
    }

    public function bbout()
    {
        return $this->hasMany(Bbout::class);
    }

    protected $table = 'suppliers';
    // protected $primaryKey = 'supplier_name';
}
