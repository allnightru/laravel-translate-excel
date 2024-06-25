<?php

namespace Ins\LaravelTranslateExcel;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TranslationExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(public array $translations) {}

    public function sheets(): array
    {
        $sheets = [];
        foreach (array_keys($this->translations) as $sheetName) {
            $sheets[] = new TranslationSheet($sheetName, $this->translations[$sheetName]);
        }

        return $sheets;
    }
}
