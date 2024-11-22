<?php

namespace App\Exports;

use App\Models\Bbinadj;
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


class BbinadjExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    use Exportable;
    private $rowNumber = 0;
    private $records;
    protected $columns = ['document_code','document_date', 'pib_number', 'seri_number', 'qty_before', 'qty_after', 'notes', 'adjust_date', 'user_name']; // Define the columns you want to export

    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        // return Bbinadj::select($this->columns)->get();
        return Bbinadj::whereIn('id', $this->records)->get();
        
        $emptyRow = collect([['', '']]); // Adjust number of empty cells based on your columns
        return $emptyRow->merge($data);
    }

    public function headings(): array
    {
        return [
            'No.',
            'Jenis Dokumen',
            'Tanggal',
            'No Dok',
            'Seri Barang',
            'Qty Lama',
            'Qty Baru',
            'Remark',
            'Tanggal Penyesuaian',
            'PIC'
        ];
    }

    public function map($bbinadj): array
    {

        $this->rowNumber++;

        return [
            $this->rowNumber,
            $bbinadj->document_code ?? 'n/a',
            Carbon::parse($bbinadj->document_date)->format('d-m-Y'),
            $bbinadj->pib_number,
            $bbinadj->seri_number,
            $bbinadj->qty_before,
            $bbinadj->qty_after,
            $bbinadj->notes,
            Carbon::parse($bbinadj->adjust_date)->format('d-m-Y'),
            $bbinadj->user_name
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
                $sheet->mergeCells('A1:J1');

                // Set the value of the merged cell
                $sheet->setCellValue('A1', 'BB Masuk Adjustment');

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
