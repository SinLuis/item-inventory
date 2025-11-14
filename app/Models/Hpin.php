<?php

namespace App\Models;

use App\Models\Log;
use App\Models\Stockcard;
use App\Models\Waste;
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
        'wips_id',
        'fg_id',
        'fg_code',
        'fg_description',
        'fg_uofm',
        'fg_quantity',
        'fg_quantity_remaining',
        'wip_id',
        'wip_code',
        'wip_description',
        'wip_uofm',
        'wip_quantity',
        'wip_quantity_remaining',
        'pib_number',
        'seri_number',
        'sub_quantity',
        'storages_id',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    protected static function booted()
    {

        parent::boot();


        static::saving(function ($hpin) {
            if (is_null($hpin->fg_quantity_remaining)) {
                $hpin->fg_quantity_remaining = ($hpin->fg_quantity ?? 0) + ($hpin->sub_quantity ?? 0);
            }
            if (is_null($hpin->wip_quantity_remaining)) {
                $hpin->wip_quantity_remaining = ($hpin->wip_quantity ?? 0);
            }
        });

        
        static::created(function ($hpin) {   
            $bboutQty = $hpin->bbout->use_quantity ?? 0;   

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
                'item_id' => $hpin->fg_id,
                'total_quantity' => $hpin->fg_quantity + $hpin->sub_quantity + $hpin->wip_quantity,
                'storages_id' => $hpin->storages_id,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

            if($hpin->bbout_id != null){
                    Waste::create([
                    'document_number' => $hpin->document_number,
                    'document_date' => $hpin->document_date,
                    'pib_number' => $hpin->pib_number,
                    'seri_number' => $hpin->seri_number,
                    'bbout_id' => $hpin->bbout_id,
                    'item_id' => 25,
                    'item_code' => 'WST-001',
                    'item_description' => 'WASTE',
                    'item_uofm' => 'KG',
                    'total_quantity' => $bboutQty - ($hpin->fg_quantity + $hpin->sub_quantity + $hpin->wip_quantity),
                    'item_amount' => 0,
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name 
                    // Tambahkan field lain yang diperlukan
                ]);
            }
            
        });
    }

    public function bbin()
    {
        return $this->belongsTo(Bbin::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function hpout()
    {
        return $this->hasMany(Hpout::class);
    }

    public function wip()
    {
        return $this->hasOne(Hpout::class, 'wips_id');
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

    public function fg()
    {
        return $this->belongsTo(Item::class);
    }
}
