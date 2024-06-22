<?php

namespace Ins\LaravelTranslateExcel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
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

        return match ($this->argument('mode')) {
            'to' => $this->to(),
            'from' => $this->from(),
        };
    }

    protected function to()
    {

        $translations = [];
        foreach (config('translate-excel.locales') as $locale) {
            $files = [];

            $dirIterator = new \RecursiveDirectoryIterator(base_path('lang/'.$locale));
            $iterator = new \RecursiveIteratorIterator($dirIterator);

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[] = str($file->getPathname())
                        ->after(base_path('lang/'.$locale.'/'));
                }
            }

            foreach ($files as $file) {
                if (in_array($file, config('translate-excel.exclude'))) {
                    continue;
                }

                $filenameKey = explode('.', $file)[0];
                if (! isset($translations[$filenameKey])) {
                    $translations[$filenameKey] = [];
                }
                $keys = array_keys(Arr::dot(Lang::get($filenameKey)));
                foreach ($keys as $key) {
                    if (! isset($translations[$filenameKey][$key])) {
                        $translations[$filenameKey][$key] = [];
                    }
                    $translation = Lang::get($filenameKey.'.'.$key, [], $locale);
                    if (
                        $translation === $filenameKey.'.'.$key
                        && $locale !== config('translate-excel.main_locale')
                    ) {
                        $translation = '';
                    }
                    $translations[$filenameKey][$key][$locale] = $translation;
                }
            }
        }

        $newTranslations = [];

        // we need to replace '/' to '->' because of excel removes '/' in sheet names
        foreach ($translations as $key => $value) {
            $newTranslations[str($key)->replace('/', '->')->value()] = $value;
        }

        Excel::store(new TranslationExport($newTranslations), $this->argument('filename'));

        return self::SUCCESS;
    }

    protected function from()
    {
        $import = new TranslationImport();
        $import->import($this->argument('filename'));

        $import->sheetNames = array_map(fn ($item) => str($item)->replace('->', '/')->value(), $import->sheetNames);

        foreach (config('translate-excel.locales') as $locale) {
            for ($k = 0; $k < count($import->sheetNames); $k++) {
                $currentTranslation = array_map(fn ($item) => $item[$locale], $import->sheetData[$k]);

                foreach ($currentTranslation as $key => $value) {
                    if (empty($value)) {
                        unset($currentTranslation[$key]);
                    }
                }

                $translation = Arr::undot($currentTranslation);

                if (! empty($translation)) {
                    if (str($import->sheetNames[$k])->contains('/')) {
                        $dirName = str($import->sheetNames[$k])
                            ->beforeLast('/')
                            ->prepend("lang/$locale/")
                            ->trim('/')
                            ->value();
                    } else {
                        $dirName = "lang/$locale";
                    }

                    $filePath = str($import->sheetNames[$k])
                        ->afterLast('/')
                        ->prepend("$dirName/")
                        ->append('.php')
                        ->trim('/')
                        ->value();

                    $string = var_export($translation, true);
                    $patterns = [
                        "/array \(/" => '[',
                        "/^([ ]*)\)(,?)$/m" => '$1]$2',
                        "/=>[ ]?\n[ ]+\[/" => '=> [',
                        "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
                    ];
                    $string = preg_replace(array_keys($patterns), array_values($patterns), $string);
                    if (! is_dir(storage_path($dirName))) {
                        mkdir(storage_path($dirName), 0755, true);
                    }
                    if (! file_exists(storage_path($filePath))) {
                        $fstr = fopen(storage_path($filePath), 'x');
                        fclose($fstr);
                    }
                    $fstr = fopen(storage_path($filePath), 'w+');
                    fwrite($fstr, "<?php\n\nreturn ");
                    fwrite($fstr, $string);
                    fwrite($fstr, ';');
                    fclose($fstr);
                }
            }
        }

        return self::SUCCESS;
    }
}
