<?php

namespace Tapp\FilamentGoogleAutocomplete\Concerns;

use Illuminate\Support\Arr;

trait CanFormatGoogleParams
{
    public function getFormattedCountries(string|array $countries): ?string
    {
        $countries = Arr::wrap($countries);

        if (count($countries) > 0) {
            $result = array_map(function ($country) {
                return sprintf('country:%s|', $country);
            }, $countries);

            return rtrim(implode('', $result), '|');
        }

        return null;
    }

    public function getFormattedPlaceTypes(string|array $placeTypes): ?string
    {
        $placeTypes = Arr::wrap($placeTypes);

        if (count($placeTypes) > 0) {
            $result = array_map(function ($placeType) {
                return sprintf('%s|', $placeType);
            }, $placeTypes);

            return rtrim(implode('', $result), '|');
        }

        return null;
    }
}
