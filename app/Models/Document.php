<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;


    protected $fillable = [
        'code',
        'description'
        // Add other attributes here
    ];

    public function bbin()
    {
        return $this->hasMany(Bbin::class);
    }

    protected $table = 'documents';
}
