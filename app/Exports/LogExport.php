<?php

namespace App\Exports;

use App\Models\Log;
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

class LogExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    use Exportable;
    private $rowNumber = 0;
    private $records;
    protected $columns = ['pib_number','seri_number', 'transaction_description', 'log_date', 'user_name']; // Define the columns you want to export

    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        // return Log::select($this->columns)->get();
        return Log::whereIn('id', $this->records)->get();
        
        $emptyRow = collect([['', '']]); // Adjust number of empty cells based on your columns
        return $emptyRow->merge($data);
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nomor PIB',
            'Nomor Seri',
            'Jenis Transaksi',
            'Tanggal Input',
            'PIC'
        ];
    }

    public function map($log): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $log->pib_number ?? 'n/a',
            $log->seri_number,
            $log->transaction_description,
            Carbon::parse($log->log_date)->format('d-m-Y'),
            $log->user_name
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
                $sheet->mergeCells('A1:F1');

                // Set the value of the merged cell
                $sheet->setCellValue('A1', 'Log');

                // Optionally apply some styling
                // 1 Open
                $sheet->getStyle('A1:F1')->applyFromArray([
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
                $sheet->getStyle('A2:F2')->applyFromArray([
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
