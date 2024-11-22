<?php

namespace App\Exports;

use App\Models\Stockcard;
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

class StockcardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    private $rowNumber = 0;
    private $records;
    protected $columns = ['pib_number','seri_number', 'document_date', 'transaction_description', 'transaction_type', 'total_quantity', 'storages_id']; // Define the columns you want to export

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        // return Log::select($this->columns)->get();
        return Stockcard::whereIn('id', $this->records)->get();
        
        $emptyRow = collect([['', '']]); // Adjust number of empty cells based on your columns
        return $emptyRow->merge($data);
    }


    public function headings(): array
    {
        return [
            'No.',
            'Nomor PIB',
            'Nomor Seri',
            'Tanggal Transaksi',
            'Tipe Transaksi',
            'Class Item',
            'Item Code',
            'Item Description',
            'Qty In',
            'Qty Out',
            'Satuan',
            'Gudang'
        ];
    }

    public function map($stockcard): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $stockcard->pib_number ?? 'n/a',
            $stockcard->seri_number,
            Carbon::parse($stockcard->documentdate)->format('d-m-Y'),
            $stockcard->transaction_description,
            $stockcard->item->class->code,
            $stockcard->item->code,
            $stockcard->item->description,
            $stockcard->transaction_type == 1 ? $stockcard->total_quantity : 0,
            $stockcard->transaction_type == 2 ? $stockcard->total_quantity : 0,
            $stockcard->item->code,
            $stockcard->storages->storage
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
                $sheet->mergeCells('A1:L1');

                // Set the value of the merged cell
                $sheet->setCellValue('A1', 'Stock Card');

                // Optionally apply some styling
                // 1 Open
                $sheet->getStyle('A1:L1')->applyFromArray([
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
                $sheet->getStyle('A2:L2')->applyFromArray([
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
