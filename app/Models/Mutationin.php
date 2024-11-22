<?php

namespace App\Models;

use App\Models\Stockcard;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutationin extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'mutationout_id',
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
        static::created(function ($mutationin) {
            // Membuat entri baru di Bbin setelah Mutationin dibuat
            $bbin = Bbin::find($mutationin->bbin_id);
            $documentDate = Carbon::parse($mutationin->document_date); // Parsing tanggal
            $createDate = Carbon::parse($mutationin->created_at);
            $diffDays = $documentDate->diffInDays($createDate);
            Bbin::create([
                'document_id' => $bbin->document_id,
                'document_number' => $bbin->document_number,
                'document_date' => $bbin->document_date,
                'seri_number' => $bbin->seri_number,
                'reff_number' => $bbin->reff_number,
                'reff_date' => $bbin->reff_date,
                'item_id' => $bbin->item_id,
                'item_description' => $bbin->item_description,
                'item_longdescription' => $bbin->item_longdescription,
                'item_uofm' => $bbin->item_uofm,
                'total_container' => $bbin->total_container,
                'total_quantity' => $mutationin->move_quantity,
                'quantity_remaining' => $mutationin->move_quantity,
                'currency_id' => $bbin->currency_id,
                'item_amount' => $bbin->item_amount,
                'storages_id' => $mutationin->storagesin_id,
                'subkontrak_id' => $bbin->subkontrak_id,
                'supplier_id' => $bbin->supplier_id,
                'country_id' => $bbin->country_id,
                'kurs' => $bbin->kurs,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

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
                'pib_number' => $mutationin->pib_number,
                'seri_number' => $mutationin->seri_number,
                'transaction_description' => 'Gudang Masuk',
                'log_date' => $documentDate,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name 
                // Tambahkan field lain yang diperlukan
            ]);

            Stockcard::create([
                'pib_number' => $mutationin->pib_number,
                'seri_number' => $mutationin->seri_number,
                'document_date' => $mutationin->document_date,
                'transaction_id' => $mutationin->id,
                'transaction_description' =>'Gudang Masuk',
                'transaction_type' => 1,
                'item_id' => $mutationin->item_id,
                'total_quantity' => $mutationin->move_quantity,
                'storages_id' => $mutationin->storagesin_id,
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

    public function storagein()
    {
        return $this->belongsTo(Storage::class);
    }

    public function storageout()
    {
        return $this->belongsTo(Storage::class, 'storagesout_id');
    }

    public function mutationout()
    {
        return $this->belongsTo(Mutationout::class);
    }
}
