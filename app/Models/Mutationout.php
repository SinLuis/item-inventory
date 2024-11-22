<?php

namespace App\Models;

use App\Models\Stockcard;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutationout extends Model
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
        'storagesout_id',
        'storagesout_desc',
        'storagesin_id',
        'move_quantity',
        'notes',
        'user_id',
        'user_name'
        // Add other attributes here
    ];

    protected static function booted()
    {
        static::created(function ($mutationout) {
            // Pastikan document_date diparse dengan benar menjadi objek Carbon
            $documentDate = Carbon::parse($mutationout->document_date); // Parsing tanggal
            $createDate = Carbon::parse($mutationout->created_at);
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
                'pib_number' => $mutationout->pib_number,
                'seri_number' => $mutationout->seri_number,
                'transaction_description' => 'Gudang Keluar',
                'log_date' => $documentDate,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

            Stockcard::create([
                'pib_number' => $mutationout->pib_number,
                'seri_number' => $mutationout->seri_number,
                'document_date' => $mutationout->document_date,
                'transaction_id' => $mutationout->id,
                'transaction_description' =>'Gudang Keluar',
                'transaction_type' => 2,
                'item_id' => $mutationout->item_id,
                'total_quantity' => $mutationout->move_quantity,
                'storages_id' => $mutationout->bbin->storages_id,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

            
        });
    }

    // public function mutationout()
    // {
    //     return $this->belongsTo(Mutationout::class, 'mutationout_id');
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bbin()
    {
        return $this->belongsTo(Bbin::class);
    }

    public function storagein()
    {
        return $this->belongsTo(Storage::class);
    }

    public function storageout()
    {
        return $this->belongsTo(Storage::class, 'storagesout_id');
    }

    public function mutationin()
    {
        return $this->hasOne(Mutationin::class);
    }
}
