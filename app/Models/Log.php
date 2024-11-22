<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'pib_number',
        'seri_number',
        'transaction_description',
        'log_date',
        'user_id',
        'user_name',
        // Add other attributes here
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
