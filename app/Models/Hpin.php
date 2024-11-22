<?php

namespace App\Models;

use App\Models\Stockcard;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hpin extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'bbout_id',
        'item_id',
        'item_code',
        'item_description',
        'item_uofm',
        'pib_number',
        'seri_number',
        'produce_quantity',
        'quantity_remaining',
        'sub_quantity',
        'storages_id',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    protected static function booted()
    {

        parent::boot();

        static::saving(function ($hpin  ) {
            if (is_null($hpin->quantity_remaining)) {
                $hpin->quantity_remaining = ($hpin->produce_quantity ?? 0) + ($hpin->sub_quantity ?? 0);
            }
        });

        static::created(function ($hpin) {
            // Pastikan document_date diparse dengan benar menjadi objek Carbon
            $documentDate = Carbon::parse($hpin->document_date); // Parsing tanggal
            $createDate = Carbon::parse($hpin->created_at);
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
                'pib_number' => $hpin->pib_number,
                'seri_number' => $hpin->seri_number,
                'transaction_description' => 'Hasil Produksi Masuk',
                'log_date' => $documentDate,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

            Stockcard::create([
                'pib_number' => $hpin->pib_number,
                'seri_number' => $hpin->seri_number,
                'document_date' => $hpin->document_date,
                'transaction_id' => $hpin->id,
                'transaction_description' =>'Hasil Produksi Masuk',
                'transaction_type' => 1,
                'item_id' => $hpin->item_id,
                'total_quantity' => $hpin->produce_quantity + $hpin->sub_quantity,
                'storages_id' => $hpin->storages_id,
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

    public function hpout()
    {
        return $this->hasMany(Hpout::class);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class,'storages_id');
    }

    public function bbout()
    {
        return $this->belongsTo(Bbout::class, 'bbout_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
