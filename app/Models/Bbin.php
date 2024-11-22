<?php

namespace App\Models;

use App\Models\Log;
use App\Models\Stockcard;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bbin extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'document_number',
        'document_date',
        'seri_number',
        'reff_number',
        'reff_date',
        'item_id',
        'item_description',
        'item_longdescription',
        'item_uofm',
        'total_container',
        'total_quantity',
        'quantity_remaining',
        'currency_id',
        'item_amount',
        'storages_id',
        'subkontrak_id',
        'supplier_id',
        'country_id',
        'kurs',
        'user_id',
        'user_name'
        // Add other attributes here
    ];
    
    protected static function booted()
    {
        static::created(function ($bbin) {
            // Pastikan document_date diparse dengan benar menjadi objek Carbon
            $documentDate = Carbon::parse($bbin->document_date); // Parsing tanggal
            $createDate = Carbon::parse($bbin->created_at);
            $diffDays = $documentDate->diffInDays($createDate);

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
                'pib_number' => $bbin->document_number,
                'seri_number' => $bbin->seri_number,
                'transaction_description' => 'Bahan Baku Masuk',
                'log_date' => $documentDate,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

            Stockcard::create([
                'pib_number' => $bbin->document_number,
                'seri_number' => $bbin->seri_number,
                'document_date' => $bbin->document_date,
                'transaction_id' => $bbin->id,
                'transaction_description' =>'Bahan Baku Masuk',
                'transaction_type' => 1,
                'item_id' => $bbin->item_id,
                'total_quantity' => $bbin->total_quantity,
                'storages_id' => $bbin->storages_id,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);
        });
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class,'storages_id');
    }

    public function supsupplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function subsupplier()
    {
        return $this->belongsTo(Supplier::class, 'subkontrak_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function bbout()
    {
        return $this->hasMany(Bbout::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bbinadj()
    {
        return $this->hasMany(Bbinadj::class);
    }

    public function mutationout()
    {
        return $this->hasMany(Mutationout::class);
    }

    public function mutationin()
    {
        return $this->hasMany(Mutationin::class);
    }
}
