<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutationout extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'bbin',
        'bbin_num',
        'bbin_seri',
        'item_id',
        'item_description',
        'item_uofm',
        'storagesout_id',
        'storagesout_desc',
        'storagesin_id',
        'move_quantity',
        'notes',
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

    public function storagein()
    {
        return $this->belongsTo(Storage::class);
    }

    public function storageout()
    {
        return $this->belongsTo(Storage::class, 'storagesout_id');
    }
}
