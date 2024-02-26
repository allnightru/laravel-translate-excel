<?php

namespace Ins\LaravelTranslateExcel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Ins\LaravelTranslateExcel\TranslationExport;
use Ins\LaravelTranslateExcel\TranslationImport;
use Maatwebsite\Excel\Facades\Excel;

class LaravelTranslateExcelCommand extends Command
{
    public $signature = 'lang:convert {mode=to} {filename=translations.xlsx}';

    public $description = 'Convert translations from/to Excel file';

    public function handle(): int
    {


        return match($this->argument('mode')) {
            'to' => $this->to(),
            'from' => $this->from(),
        };
    }

    protected function to() {

        $translations = [];
        foreach(config('translate-excel.locales') as $locale) {
            $files = File::files(base_path('lang/'.$locale));
            foreach($files as $file) {
                $filenameKey = explode(".", $file->getBasename())[0];
                if(!isset($translations[$filenameKey])) {
                    $translations[$filenameKey] = [];
                }
                $keys = array_keys(Arr::dot(Lang::get($filenameKey)));
                foreach($keys as $key) {
                    if(!isset($translations[$filenameKey][$key])) {
                        $translations[$filenameKey][$key] = [];
                    }
                    $translation = Lang::get($filenameKey.".".$key, [],$locale);
                    if($translation === Lang::get($filenameKey.".".$key, [], config('translate-excel.main_locale')) && $locale !== config('translate-excel.main_locale')) {
                        $translation = '';
                    }
                    $translations[$filenameKey][$key][$locale] = $translation;
                }
            }
        }

        Excel::store(new TranslationExport($translations), $this->argument('filename'));

        return self::SUCCESS;
    }

    protected function from() {
        $import = new TranslationImport();
        $import->import($this->argument('filename'));
        $translations = [];

        foreach(config('translate-excel.locales') as $locale) {
            for($k=0;$k<count($import->sheetNames);$k++) {
                $currentTranslation = array_map(function($item) use ($locale) {return $item[$locale];}, $import->sheetData[$k]);

                foreach($currentTranslation as $key=>$value) {
                    if(empty($value)) {
                        unset($currentTranslation[$key]);
                    }
                }

                $translation = Arr::undot($currentTranslation);

                if(!empty($translation)) {

                    $string = var_export($translation, true);
                    $patterns = [
                        "/array \(/" => '[',
                        "/^([ ]*)\)(,?)$/m" => '$1]$2',
                        "/=>[ ]?\n[ ]+\[/" => '=> [',
                        "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
                    ];
                    $string = preg_replace(array_keys($patterns), array_values($patterns), $string);
                    if (!is_dir(storage_path('lang/' . $locale))) {
                        mkdir(storage_path('lang/' . $locale), 0755, true);
                    }
                    if (!file_exists(storage_path('lang/' . $locale . '/' . $import->sheetNames[$k] . '.php'))) {
                        $fstr = fopen(storage_path('lang/' . $locale . '/' . $import->sheetNames[$k] . '.php'), "x");
                        fclose($fstr);
                    }
                    $fstr = fopen(storage_path('lang/' . $locale . '/' . $import->sheetNames[$k] . '.php'), "w+");
                    fputs($fstr, '<?php
                return ');
                    fputs($fstr, $string);
                    fputs($fstr, ';');
                    fclose($fstr);
                }
            }
        }
        return self::SUCCESS;
    }
}
