<?php

namespace App\Exports;

use NumberFormatter;
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
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class StockcardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    private $rowNumber = 0;
    private $records;
    protected $columns = ['pib_number','seri_number', 'document_date', 'transaction_description', 'transaction_type', 'total_quantity', 'storages_id']; // Define the columns you want to export
    private $startDate;
    private $endDate;  
    private $itemCode;

    public function __construct(array $records, $startDate = null, $endDate = null, $itemId = null, $itemCode = null, $itemDescription = null, $itemClass = null, $storageId = null, $storageDescription = null, $userExport = null, $dateExport = null)
    {
        $this->records = $records;
        if ($startDate && $endDate) {
            $this->startDate = Carbon::parse($startDate)->format('d M Y');
            $this->endDate = Carbon::parse($endDate)->format('d M Y');
            
        }
        $this->itemId = $itemId;
        $this->itemCode = $itemCode;
        $this->itemDescription = $itemDescription;
        $this->itemClass = $itemClass;
        $this->storageId = $storageId;
        $this->storageDescription = $storageDescription;
        $this->userExport = $userExport;
        $this->dateExport = $dateExport;
    }


    public function collection()
    {
        // return Log::select($this->columns)->get();
        return Stockcard::whereIn('id', $this->records) ->orderBy('document_date', 'asc')->get();
        
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
            'Gudang',
            // 'Class Item',
            // 'Item Code',
            // 'Item Description',
            'Quantity In',
            'Quantity Out',
            'Quantity'
            // 'Satuan',
        ];
    }
    
    public function map($stockcard): array
    {
        $this->rowNumber++;
        // dd($stockcard);
        $beginningBalance = Stockcard::where('item_id', $stockcard->item_id)
            ->where('storages_id', $this->storageId)
            ->where(function ($query) use ($stockcard) {
                $query->where('document_date', '<', $stockcard->document_date)
                    ->orWhere(function ($subQuery) use ($stockcard) {
                        $subQuery->where('document_date', '=', $stockcard->document_date)
                                ->where('created_at', '<', $stockcard->created_at);
                    });
            })
            ->selectRaw("
                SUM(CASE 
                    WHEN transaction_type = 1 THEN total_quantity 
                    WHEN transaction_type = 2 THEN -total_quantity 
                    ELSE 0 
                END) as balance
            ")
            ->value('balance') ?? 0;
        // Adjust with the current record's transaction
        $currentQuantity = $stockcard->transaction_type == 1 
            ? $stockcard->total_quantity 
            : -$stockcard->total_quantity;
  
        $endingBalance = $beginningBalance + $currentQuantity;
        if ($endingBalance == 0){
            $endingBalance = "0";
        }else{
            $endingBalance;
        }
        
        return [
            $this->rowNumber,
            $stockcard->pib_number ?? 'n/a',
            $stockcard->seri_number,
            Carbon::parse($stockcard->document_date)->format('d-m-Y'),
            $stockcard->transaction_description,
            $stockcard->storages->storage,
            // $stockcard->item->class->code,
            // $stockcard->item->code,
            // $stockcard->item->description,
            $stockcard->transaction_type == 1 ? $stockcard->total_quantity : 0,
            $stockcard->transaction_type == 2 ? $stockcard->total_quantity : 0,
            $endingBalance,
            // $stockcard->item->uofm->code,
            
        ];
    }

    public function startCell(): string
    {
        return 'A9'; // Specify the starting cell here

    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->mergeCells('E1:I1');
                $sheet->setShowGridlines(false);
                $sheet->setCellValue('A1', 'Stock Card');
                $sheet->setCellValue('E1', 'PT. PAHALA BAHARI NUSANTARA');
                
                $beginningBalance = 0;
                $quantityIn = 0;
                $quantityOut = 0;
                $endingBalance = 0;
                if ($this->startDate && $this->endDate) {
                    $beginningBalance = Stockcard::where('item_id', $this->itemId)
                        ->where('storages_id', $this->storageId)
                        ->where('document_date', '<', Carbon::parse($this->startDate)->format('Y-m-d'))
                        ->selectRaw("SUM(CASE WHEN transaction_type = 1 THEN total_quantity WHEN transaction_type = 2 THEN -total_quantity ELSE 0 END) as balance")
                        ->value('balance') ?? 0;
                    $quantityIn = Stockcard::where('item_id', $this->itemId)
                        ->where('storages_id', $this->storageId)
                        ->whereBetween('document_date', [
                            Carbon::parse($this->startDate)->format('Y-m-d'),
                            Carbon::parse($this->endDate)->format('Y-m-d')
                        ])
                        ->selectRaw("SUM(CASE WHEN transaction_type = 1 THEN total_quantity ELSE 0 END) as balance")
                        ->value('balance') ?? 0;
                    $quantityOut = Stockcard::where('item_id', $this->itemId)
                        ->where('storages_id', $this->storageId)
                        ->whereBetween('document_date', [
                            Carbon::parse($this->startDate)->format('Y-m-d'),
                            Carbon::parse($this->endDate)->format('Y-m-d')
                        ])
                        ->selectRaw("SUM(CASE WHEN transaction_type = 2 THEN total_quantity ELSE 0 END) as balance")
                        ->value('balance') ?? 0;
            
                    $endingBalance = $beginningBalance + $quantityIn - $quantityOut;
                    
                }else{
                    $quantityIn = Stockcard::where('item_id', $this->itemId)
                        ->where('storages_id', $this->storageId)
                        ->selectRaw("SUM(CASE WHEN transaction_type = 1 THEN total_quantity ELSE 0 END) as balance")
                        ->value('balance') ?? 0;
                    $quantityOut = Stockcard::where('item_id', $this->itemId)
                        ->where('storages_id', $this->storageId)
                        ->selectRaw("SUM(CASE WHEN transaction_type = 2 THEN total_quantity ELSE 0 END) as balance")
                        ->value('balance') ?? 0;
                    $endingBalance = $beginningBalance + $quantityIn - $quantityOut;
                }
                // $formatter = new NumberFormatter('id_ID', NumberFormatter::DECIMAL);
                // $endingBalance = $formatter->format($endingBalance);
                // dd($beginningBalance);   
                // 1 Open
                for ($row = 1; $row <= 7; $row++) {
                    $sheet->mergeCells("A$row:D$row");
                    $fontSize = ($row == 1) ? 12 : 10; 
                    $sheet->getStyle("A$row:D$row")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => $fontSize,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }
                
                // 1 Close
              
                // 2 Open
                if ($this->startDate && $this->endDate) {
                    $sheet->setCellValue('A2', "Period: {$this->startDate} to {$this->endDate}");
                }else{
                    $sheet->setCellValue('A2', "Filtered by Date Range: All Period");
                }
                $sheet->setCellValue('A3', "Item Class : {$this->itemClass}");
                $sheet->setCellValue('A4', "Item Code: {$this->itemCode}");
                $sheet->setCellValue('A5', "Item Description : {$this->itemDescription}");
                $sheet->setCellValue('A6', "Print By: {$this->userExport}");
                $sheet->setCellValue('A7', "Print Date: {$this->dateExport}");
                // 2 Close

                // 3 Open
                $sheet->getStyle('E1:I1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
                // 3 Close
                
                // 4 Open
                $sheet->getStyle('A9:I9')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'color' => ['argb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '7B7B7B'], // Background color (green)
                    ],
                    
                ]);
                foreach (range('A', 'I') as $column) {
                    $cell = $column . '9';
                    $sheet->getStyle($cell)->applyFromArray([
                        'borders' => [
                            'top' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FFFFFF'], // White border for top
                            ],
                            'right' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FFFFFF'], // White border for right
                            ],
                            'bottom' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FFFFFF'], // White border for bottom
                            ],
                            'left' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FFFFFF'], // White border for left
                            ],
                            
                        ]
                        
                    ]);
                    
                }
                $sheet->insertNewRowBefore(10, 1); 
                $sheet->setCellValue('H10', "Qty Beginning: ");
                // dd($beginningBalance);
                $sheet->setCellValue('I10', $beginningBalance);
                // 4 Close

                // 5 Open
                $sheet->getStyle('A10:I10')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['argb' => '000000']
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFF'], // Background color (green)
                    ],
                    
                ]);
                
                // 5 Close
                // 6 Open
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                $range = "A9:{$lastColumn}{$lastRow}";
                $quantity = "G9:{$lastColumn}{$lastRow}";
                // $quantity = $formatter->format($quantity);

                $sheet->setCellValue('G' . ($lastRow + 2), $quantityIn);
                $sheet->setCellValue('H' . ($lastRow + 2), $quantityOut);
                $sheet->setCellValue('H' . ($lastRow + 3), "Qty Beginning");
                $sheet->setCellValue('H' . ($lastRow + 4), "Total Qty In");
                $sheet->setCellValue('H' . ($lastRow + 5), "Total Qty Out");
                $sheet->setCellValue('H' . ($lastRow + 6), "Qty Ending");
                $sheet->setCellValue('I' . ($lastRow + 3), "$beginningBalance");
                $sheet->setCellValue('I' . ($lastRow + 4), $quantityIn);
                $sheet->setCellValue('I' . ($lastRow + 5), $quantityOut);
                $sheet->setCellValue('I' . ($lastRow + 6), $endingBalance);
                $sheet->getStyle('G' . ($lastRow+2). ':I' .($lastRow+6))->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                            'top' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'], // White border for top
                            ],
                    ]
                ])->getNumberFormat()->setFormatCode('#,##0.00;(#,##0.00)');
                $sheet->getStyle($range)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getStyle($quantity)->getNumberFormat()->setFormatCode('#,##0.00;(#,##0.00)');
                // 6 Close

            
            },
            
        ];
    }
}

