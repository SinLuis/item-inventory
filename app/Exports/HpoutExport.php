<?php

namespace App\Exports;

use App\Models\Hpout;
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

class HpoutExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{

    use Exportable;
    private $rowNumber = 0;
    protected $columns = ['document_number', 'document_date', 'sj_number', 'sj_date', 'customer_id', 'country_id', 'item_id', 'item_description', 'item_uofm', 'total_quantity', 'currency_id', 'item_amount'];
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Hpout::select($this->columns)->get();
        
        $emptyRow = collect([['', '']]); // Adjust number of empty cells based on your columns
        return $emptyRow->merge($data);
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nomor PEB',
            'Tanggal PEB',
            'Nomor Bukti Keluar',
            'Tanggal Bukti Keluar',
            'Pembeli/Penerima',
            'Negara Tujuan',
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Jumlah',
            'Mata Uang',
            'Nilai Barang',
        ];
    }
    public function map($hpout): array
    {

        $this->rowNumber++;

        return [
            $this->rowNumber,
            $hpout->document_number,
            Carbon::parse($hpout->document_date)->format('d-m-Y'),
            $hpout->sj_number,
            Carbon::parse($hpout->sj_date)->format('d-m-Y'),
            $hpout->customer->customer_name,
            $hpout->country->country,
            $hpout->item->code,
            $hpout->item_description,
            $hpout->item_uofm,
            $hpout->total_quantity,
            $hpout->currency->currency,
            $hpout->item_amount
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

                $sheet->mergeCells('A1:M1');

                $sheet->setCellValue('A1', 'HP Keluar');

                // Optionally apply some styling
                // 1 Open
                $sheet->getStyle('A1:M1')->applyFromArray([
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
                $sheet->getStyle('A2:M2')->applyFromArray([
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
