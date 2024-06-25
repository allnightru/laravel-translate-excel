<?php

namespace Ins\LaravelTranslateExcel;

use Ins\LaravelTranslateExcel\Commands\LaravelTranslateExcelCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelTranslateExcelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-translate-excel')
            ->hasConfigFile('translate-excel')
            ->hasCommand(LaravelTranslateExcelCommand::class);
    }
}
