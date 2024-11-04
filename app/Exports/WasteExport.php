<?php

namespace App\Exports;

use App\Models\Waste;
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

class WasteExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    private $rowNumber = 0;
    protected $columns = ['document_number', 'document_date', 'item_id', 'item_description', 'item_uofm', 'total_quantity', 'item_amount'];
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Waste::select($this->columns)->get();
        
        $emptyRow = collect([['', '']]); // Adjust number of empty cells based on your columns
        return $emptyRow->merge($data);
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nomor',
            'Tanggal',
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Jumlah',
            'Nilai Barang',
        ];
    }

    public function map($waste): array
    {

        $this->rowNumber++;

        return [
            $this->rowNumber,
            $waste->document_number,
            Carbon::parse($waste->document_date)->format('d-m-Y'),
            $waste->item_id,
            $waste->item_description,
            $waste->item_uofm,
            $waste->total_quantity,
            $waste->item_amount
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

                $sheet->mergeCells('A1:H1');

                $sheet->setCellValue('A1', 'WASTE');

                // Optionally apply some styling
                // 1 Open
                $sheet->getStyle('A1:H1')->applyFromArray([
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
                $sheet->getStyle('A2:H2')->applyFromArray([
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
