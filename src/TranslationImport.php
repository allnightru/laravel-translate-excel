<?php

namespace Ins\LaravelTranslateExcel;

use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class TranslationImport implements ToArray, WithEvents, WithHeadingRow
{
    use Importable;
    public array $sheetData = [];

    public array $sheetNames = [];

    public function array(array $array)
    {
        $data = [];
        foreach($array as $record) {
            $record = collect($record);
            if($record->has('key') && !empty($record->get('key'))) {
                $data[$record->get('key')] = $record->only(...config('translate-excel.locales'))->toArray();
            }
        }
        $this->sheetData[] = $data;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->sheetNames[] = $event->getSheet()->getDelegate()->getTitle();
            }
        ];
    }


}
