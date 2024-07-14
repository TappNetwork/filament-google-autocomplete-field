<?php

namespace Tapp\FilamentGoogleAutocomplete\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tapp\FilamentGoogleAutocomplete\FilamentGoogleAutocomplete
 */
class FilamentGoogleAutocomplete extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Tapp\FilamentGoogleAutocomplete\FilamentGoogleAutocomplete::class;
    }
}
