<?php

namespace App\Models;

use App\Models\Stockcard;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hpout extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'sj_number',
        'sj_date',
        'customer_id',
        'country_id',
        'hpin_id',
        'item_id',
        'item_code',
        'item_description',
        'item_longdescription',
        'item_uofm',
        'pib_number',
        'seri_number',
        'total_quantity',
        'currency_id',
        'item_amount',
        'kurs',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    protected static function booted()
    {
        static::created(function ($hpout) {
            // Pastikan document_date diparse dengan benar menjadi objek Carbon
            $documentDate = Carbon::parse($hpout->document_date); // Parsing tanggal
            $createDate = Carbon::parse($hpout->created_at);
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
                'pib_number' => $hpout->pib_number,
                'seri_number' => $hpout->seri_number,
                'transaction_description' => 'Hasil Produksi Keluar',
                'log_date' => $documentDate,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

            Stockcard::create([
                'pib_number' => $hpout->pib_number,
                'seri_number' => $hpout->seri_number,
                'document_date' => $hpout->document_date,
                'transaction_id' => $hpout->id,
                'transaction_description' =>'Hasil Produksi Keluar',
                'transaction_type' => 2,
                'item_id' => $hpout->item_id,
                'total_quantity' => $hpout->total_quantity,
                'storages_id' => $hpout->hpin->storages_id,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);
        });
    }

    public function hpin()
    {
        return $this->belongsTo(Hpin::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
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
