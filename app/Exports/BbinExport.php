<?php

namespace App\Exports;

use App\Models\Bbin;
use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;


class BbinExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{

    use Exportable;
    private $rowNumber = 0;
    protected $columns = ['document_id','document_number', 'document_date', 'seri_number', 'reff_number', 'reff_date', 'item_id', 'item_description', 'item_uofm', 'total_container', 'total_quantity', 'currency_id', 'item_amount', 'storages_id', 'subkontrak_id', 'supplier_id', 'country_id']; // Define the columns you want to export

    /**
    * @return \Illuminate\Support\Collection
    */
    

    public function collection()
    {
        return Bbin::select($this->columns)->get();
        
        $emptyRow = collect([['', '']]); // Adjust number of empty cells based on your columns
        return $emptyRow->merge($data);
    }

    public function headings(): array
    {
        return [
            'No.',
            'Jenis Dok',
            'Nomor Dok',
            'Tanggal Dok',
            'No Seri',
            'Nomor Surat',
            'Tanggal Surat',
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Jumlah Kontainer',
            'Jumlah Total',
            'Mata Uang',
            'Nilai Barang',
            'Gudang',
            'Penerima Subkontrak',
            'Pemasok Pengirim',
            'Negara Asal'
        ];
    }

    public function map($bbin): array
    {

        $this->rowNumber++;

        return [
            $this->rowNumber,
            $bbin->document->code ?? 'n/a',
            $bbin->document_number,
            Carbon::parse($bbin->document_date)->format('d-m-Y'),
            $bbin->seri_number,
            $bbin->reff_number,
            Carbon::parse($bbin->reff_date)->format('d-m-Y'),
            $bbin->item->code ?? 'n/a',
            $bbin->item_description,
            $bbin->item_uofm,
            $bbin->total_container,
            $bbin->total_quantity,
            $bbin->currency->currency,
            $bbin->item_amount,
            $bbin->storage->storage,
            $bbin->subsupplier->supplier_name ?? 'Nihil',
            $bbin->supsupplier->supplier_name,
            $bbin->country->country
        ];
    }

    public function startCell(): string
    {
        return 'A2'; // Specify the starting cell here
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge cells from A1 to R1
                $sheet->mergeCells('A1:R1');

                // Set the value of the merged cell
                $sheet->setCellValue('A1', 'BB Masuk');

                // Optionally apply some styling
                // 1 Open
                $sheet->getStyle('A1:R1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
                // 1 Close

                // 2 Open
                $sheet->getStyle('A2:R2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                    ],
                ]);
                // 2 Close
            },
            
        ];
    }

    
}
