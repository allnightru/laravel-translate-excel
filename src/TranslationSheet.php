<?php

namespace Ins\LaravelTranslateExcel;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TranslationSheet implements FromArray, ShouldAutoSize, WithColumnWidths, WithEvents, WithHeadings, WithStyles, WithTitle
{
    public function __construct(protected string $name, protected array $data)
    {
        $this->data = array_map(function (string $key, array $value) {
            return ['key' => $key, ...$value];
        }, array_keys($data), array_values($data));

    }

    public function title(): string
    {
        return $this->name;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['key', ...config('translate-excel.locales')];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 25,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 25,
            'I' => 25,
            'J' => 25,
            'K' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A' => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $workSheet = $event->sheet->getDelegate();
                $workSheet->freezePane('B2'); // freezing here

                $workSheet->getStyle('A1:Z1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('d0faa6');

                $workSheet->getStyle('A2:A999')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('a6e5fa');
            },
        ];
    }
}
