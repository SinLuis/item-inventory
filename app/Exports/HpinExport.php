<?php

namespace App\Exports;

use App\Models\Hpin;
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

class HpinExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    use Exportable;
    private $rowNumber = 0;
    protected $columns = ['document_number', 'document_date', 'item_id', 'item_description', 'produce_quantity', 'sub_quantity', 'storages_id'];

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Hpin::select($this->columns)->get();
        
        $emptyRow = collect([['', '']]); // Adjust number of empty cells based on your columns
        return $emptyRow->merge($data);
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nomor Surat',
            'Tanggal Surat',
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'dari Produksi',
            'dari Subkontrak',
            'Gudang',
        ];
    }

    public function map($hpin): array
    {

        $this->rowNumber++;

        return [
            $this->rowNumber,
            $hpin->document_number,
            Carbon::parse($hpin->document_date)->format('d-m-Y'),
            $hpin->item_id,
            $hpin->item_description,
            $hpin->item_uofm,
            $hpin->produce_quantity,
            $hpin->sub_quantity,
            $hpin->storage->storage ?? 'Nihil',
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

                $sheet->mergeCells('A1:I1');

                $sheet->setCellValue('A1', 'HP Masuk');

                // Optionally apply some styling
                // 1 Open
                $sheet->getStyle('A1:I1')->applyFromArray([
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
                $sheet->getStyle('A2:I2')->applyFromArray([
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
