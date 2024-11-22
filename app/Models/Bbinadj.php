<?php

namespace App\Models;

use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bbinadj extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'document_code',
        'document_date',
        'bbin_id',
        'pib_number',
        'seri_number',
        'item_id',
        'qty_before',
        'qty_after',
        'notes',
        'adjust_date',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    protected static function booted()
    {
        static::created(function ($bbinadj) {
            // Pastikan document_date diparse dengan benar menjadi objek Carbon
            $documentDate = Carbon::parse($bbinadj->document_date); // Parsing tanggal
            $createDate = Carbon::parse($bbinadj->created_at);
            $diffDays = $documentDate->diffInDays($createDate);
            $transactionDescription = 'Adjustment Bahan Baku Masuk';
            $transactionType = 1;
            $transactionDiff = 0;
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
                'pib_number' => $bbinadj->pib_number,
                'seri_number' => $bbinadj->seri_number,
                'transaction_description' => 'Adjustment Bahan Baku Masuk',
                'log_date' => $documentDate,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

            if($bbinadj->qty_before < $bbinadj->qty_after){
                $transactionDescription = 'Adjustment Bahan Baku Masuk (+)';
                $transactionType = 1;
                $transactionDiff = $bbinadj->qty_after - $bbinadj->qty_before;
            } else {
                $transactionDescription = 'Adjustment Bahan Baku Masuk (-)';
                $transactionType = 2;
                $transactionDiff = $bbinadj->qty_before - $bbinadj->qty_after;
            }

            Stockcard::create([
                'pib_number' => $bbinadj->pib_number,
                'seri_number' => $bbinadj->seri_number,
                'document_date' => $bbinadj->adjust_date,
                'transaction_id' => $bbinadj->id,
                'transaction_description' =>  $transactionDescription,
                'transaction_type' => $transactionType,
                'item_id' => $bbinadj->bbin->item_id,
                'total_quantity' => $transactionDiff,
                'storages_id' => $bbinadj->bbin->storages_id,
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
