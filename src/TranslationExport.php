<?php

namespace Ins\LaravelTranslateExcel;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;

class TranslationExport implements WithMultipleSheets
{
    use Exportable;
    public function __construct(public array $translations)
    {
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach(array_keys($this->translations) as $sheetName) {
            $sheets[] = new TranslationSheet($sheetName, $this->translations[$sheetName]);
        }
        return $sheets;
    }
}
