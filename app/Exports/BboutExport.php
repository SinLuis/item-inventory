<?php

namespace App\Exports;

use App\Models\Bbout;
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

class BboutExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{

    use Exportable;
    private $rowNumber = 0;
    protected $columns = ['document_number', 'document_date', 'item_id', 'item_description', 'item_uofm', 'use_quantity', 'sub_quantity', 'subkontrak_id','notes'];

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Bbout::select($this->columns)->get();
        
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
            'Digunakan',
            'Disubkontrakan',
            'Penerima Subkontrak',
            'Keterangan',
        ];
    }

    public function map($bbout): array
    {

        $this->rowNumber++;

        return [
            $this->rowNumber,
            $bbout->document_number,
            Carbon::parse($bbout->document_date)->format('d-m-Y'),
            $bbout->item_id,
            $bbout->item_description,
            $bbout->item_uofm,
            $bbout->use_quantity,
            $bbout->sub_quantity,
            $bbout->subsupplier->supplier_name ?? 'Nihil',
            $bbout->notes
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

                $sheet->mergeCells('A1:J1');

                $sheet->setCellValue('A1', 'BB Keluar');

                // Optionally apply some styling
                // 1 Open
                $sheet->getStyle('A1:J1')->applyFromArray([
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
                $sheet->getStyle('A2:J2')->applyFromArray([
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
