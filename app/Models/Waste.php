<?php

namespace App\Models;

use App\Models\Stockcard;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'pib_number',
        'seri_number',
        'bbout_id',
        'item_id',
        'item_code',
        'item_description',
        'item_uofm',
        'total_quantity',
        'item_amount',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    protected static function booted()
    {
        static::created(function ($waste) {
            // Pastikan document_date diparse dengan benar menjadi objek Carbon
            $documentDate = Carbon::parse($waste->document_date); // Parsing tanggal
            $createDate = Carbon::parse($waste->created_at);
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
                'pib_number' => $waste->pib_number,
                'seri_number' => $waste->seri_number,
                'transaction_description' => 'Waste',
                'log_date' => $documentDate,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

            // Stockcard::create([
            //     'pib_number' => $waste->pib_number,
            //     'seri_number' => $waste->seri_number,
            //     'transaction_id' => $waste->id,
            //     'transaction_description' =>'Waste',
            //     'transaction_type' => 1,
            //     'item_id' => $waste->item_id,
            //     'total_quantity' => $waste->total_quantity,
            //     'storages_id' => $waste->bbout->bbin->storages_id,
            //     'user_id' => auth()->id(),
            //     'user_name' => auth()->user()->name 
            //     // Tambahkan field lain yang diperlukan
            // ]);
        });
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function bbout()
    {
        return $this->belongsTo(Bbout::class);
    }

    public function hpin()
    {
        return $this->belongsTo(HPIN::class);
    }
}
