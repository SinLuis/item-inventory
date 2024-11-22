<?php

namespace App\Models;

use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bbout extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'bbin_id',
        'pib_number',
        'seri_number',
        'item_id',
        'item_code',
        'item_description',
        'item_uofm',
        'subkontrak_name',
        'subkontrak_id',
        'use_quantity',
        'quantity_remaining',
        'sub_quantity',
        'subkontrak_id',
        'notes',
        'fg_id',
        'fg_description',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    protected static function booted()
    {
        parent::boot();

        static::saving(function ($bbout) {
            if (is_null($bbout->quantity_remaining)) {
                $bbout->quantity_remaining = ($bbout->use_quantity ?? 0) + ($bbout->sub_quantity ?? 0);
            }
        });
        
        static::created(function ($bbout) {
            // Pastikan document_date diparse dengan benar menjadi objek Carbon
            $documentDate = Carbon::parse($bbout->document_date); // Parsing tanggal
            $createDate = Carbon::parse($bbout->created_at);
            $diffDays = $documentDate->diffInDays($createDate);
            // dd($createDate);

            if ($diffDays<2) {
                $documentDate = $createDate;
            }             
            else {
                if ($documentDate->isFriday()) {
                    $documentDate->addDays(4);
                } 
                else if ($documentDate->isThursday()) {
                    $documentDate->addDays(4); 
                }                
                else {
                    $documentDate->addDays(2); 
                }
            }
    
            Log::create([
                'pib_number' => $bbout->pib_number,
                'seri_number' => $bbout->seri_number,
                'transaction_description' => 'Bahan Baku Keluar',
                'log_date' => $documentDate,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

            Stockcard::create([
                'pib_number' => $bbout->pib_number,
                'seri_number' => $bbout->seri_number,
                'document_date' => $bbout->document_date,
                'transaction_id' => $bbout->id,
                'transaction_description' =>'Bahan Baku Keluar',
                'transaction_type' => 2,
                'item_id' => $bbout->bbin->item_id,
                'total_quantity' => $bbout->use_quantity + $bbout->sub_quantity,
                'storages_id' => $bbout->bbin->storages_id,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);
        });
    }
    
    public function bbin()
    {
        return $this->belongsTo(Bbin::class);
    }

    public function subsupplier()
    {
        return $this->belongsTo(Supplier::class, 'subkontrak_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'fg_id');
    }

    
    public function setQtyAttribute($value)
    {
        $this->attributes['sub_quantity'] = $value !== null ? $value : '0';
    }

    public function hpin()
    {
        return $this->hasMany(Hpin::class, 'id');
    }

    public function waste()
    {
        return $this->hasMany(Waste::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

  

}
