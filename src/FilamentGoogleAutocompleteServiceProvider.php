<?php

namespace Tapp\FilamentGoogleAutocomplete;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentGoogleAutocompleteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-google-autocomplete-field')
            ->hasConfigFile()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        //
    }
}
