# Filament Google Autcomplete Field

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tapp/filament-google-autocomplete-field.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-google-autocomplete-field)
![GitHub Tests Action Status](https://github.com/TappNetwork/filament-google-autocomplete-field/actions/workflows/run-tests.yml/badge.svg)
![GitHub Code Style Action Status](https://github.com/TappNetwork/filament-google-autocomplete-field/actions/workflows/fix-php-code-style-issues.yml/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/tapp/filament-google-autocomplete-field.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-google-autocomplete-field)

This plugin provides an address autocomplete using [Google Place autocomplete API](https://developers.google.com/maps/documentation/places/web-service/autocomplete), with fully customizable address fields.

> [!NOTE]
> The package(https://github.com/SachinAgarwal1337/google-places-api) is used to make API requests to Google Places.

## Installation

You can install the package via composer:

```bash
composer require tapp/filament-google-autocomplete-field
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-google-autocomplete-field-config"
```

This is the contents of the published config file:

```php
return [

    'api-key' => env('GOOGLE_PLACES_API_KEY', ''),

];

```

Optionally, you can publish the translations files with:

```bash
php artisan vendor:publish --tag="filament-google-autocomplete-field-translations"
```

## Setup

On [Google console](https://console.cloud.google.com/apis/dashboard), create an application and enable the Places API.

1. click on "ENABLE APIS AND SERVICES"
2. search for "Places api"
3. click to enable it
4. get the API key

In your Laravel application, add the Google API key to `GOOGLE_PLACES_API_KEY` in your `.env` file:

```php
GOOGLE_PLACES_API_KEY=your_google_place_api_key_here
```

## Appareance

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete01.png)

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete02.png)

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete03.png)

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete04.png)

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete05.png)

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete06.png)

## Usage

Add the `GoogleAutocomplete` field to your form:

```php
use Tapp\FilamentGoogleAutocompleteField\GoogleAutocomplete;

GoogleAutocomplete::make('google_search'),
```

This will use the default address fields and Google API options. You can also customize the address fields and Google Place API options (see the options available on `Available options` itens below). For example:


```php
use Tapp\FilamentGoogleAutocomplete\GoogleAutocomplete;

GoogleAutocomplete::make('google_search')
    ->label('Google look-up')
    ->countries([
        'US',
        'AU',
    ])
    ->language('pt-BR')
    ->withFields([
        Forms\Components\TextInput::make('address')
            ->extraInputAttributes([
                'data-google-field' => '{street_number} {route}, {sublocality_level_1}',
            ]),
        Forms\Components\TextInput::make('country'),
        Forms\Components\TextInput::make('coordinates')
            ->extraInputAttributes([
                'data-google-field' => '{latitude}, {longitude}',
            ]),
    ]),
```

## Available options

### Autocompleted Fields

You can use the `withFields` to define the fields that will be autocompleted.

By default the following fields are set if this method isn't provided:

```php
Forms\Components\TextInput::make('address')
    ->extraInputAttributes([
        'data-google-field' => '{street_number} {route}, {sublocality_level_1}',
    ]),
Forms\Components\TextInput::make('city')
    ->extraInputAttributes([
        'data-google-field' => 'locality',
    ]),
Forms\Components\TextInput::make('country'),
Forms\Components\TextInput::make('zip')
    ->extraInputAttributes([
        'data-google-field' => 'postal_code',
    ]),
```

You can override these default fields by passing an array of the Filament form fields to `withFields` method:

```php
GoogleAutocompleteFields::make('google_search')
    ->withFields([
        Forms\Components\TextInput::make('address')
            ->extraInputAttributes([
                'data-google-field' => '{street_number} {route}, {sublocality_level_1}',
            ]),
        Forms\Components\TextInput::make('city')
            ->extraInputAttributes([
                'data-google-field' => 'locality',
            ]),
    ]),
```

#### Combining Fields

You can combine multiple fields returned by Google API in one field using curly braces `{}` to wrap the fields in `'data-google-field'` extra input attribute. For example, the `address` field below will contain the `street_number`, `route`, and `sublocality_level_1` and the `coordinates` field will contain the `latitude` and `longitude` fields:

```php
Forms\Components\TextInput::make('address')
    ->extraInputAttributes([
        'data-google-field' => '{street_number} {route}, {sublocality_level_1}',
    ]),
Forms\Components\TextInput::make('coordinates')
    ->extraInputAttributes([
        'data-google-field' => '{latitude},{longitude}',
    ]),
```

#### Field Name

If your database field have a different name than the Google field (for example you DB field is `zip` and you want to use the Google's `postal_code` value returned by API), you can tie the API field to the DB field by passing the `'data-google-field'` to the `extraInputAttributes` method like so: 

```php
Forms\Components\TextInput::make('zip')
    ->extraInputAttributes([
        'data-google-field' => 'postal_code',
    ])
```

These are the names of the Google metadata fields available to use:

```
street_number,
route,
locality,
sublocality_level_1,
administrative_area_level_2,
administrative_area_level_1,
country,
postal_code,
place_id,
formatted_address,
formatted_phone_number,
international_phone_number,
name,
website,
latitude,
longitude,
```

#### long_name and short_name 

Google's API returns long_name and short_name options for address fields. You can choose which one to display by passing the `'data-google-value'` to the `extraInputAttributes` method: 

```php
Forms\Components\TextInput::make('country')
    ->extraInputAttributes([
        'data-google-value' => 'short_name',
    ])
```

E.g. of `long_name` and `short_name` data returned by Google's API:

```php
"street_number" => [
    "long_name" => "1535"
    "short_name" => "1535"
]
"route" => [
    "long_name" => "Broadway"
    "short_name" => "Broadway"
]
"locality" => [
    "long_name" => "New York"
    "short_name" => "New York"
]
"sublocality_level_1" => [
    "long_name" => "Manhattan"
    "short_name" => "Manhattan"
]
"administrative_area_level_2" => [
    "long_name" => "New York County"
    "short_name" => "New York County"
]
"administrative_area_level_1" => [
    "long_name" => "New York"
    "short_name" => "NY"
]
"country" => [
    "long_name" => "United States"
    "short_name" => "US"
] 
"postal_code" => [
    "long_name" => "10036"
    "short_name" => "10036"
]    
```

### Autocomplete Field Column Span

The default column span for autcomplete select field is `'full'`. You can define other value (same as supported by Filament's `columnSpan()`) using the `autocompleteFieldColumnSpan` method:

```php
GoogleAutocomplete::make('google_search')
    ->autocompleteFieldColumnSpan(1)
```

### Autocomplete Field Search Debounce

The default search debounce is 2 seconds to avoid too many requests to Google Places API. You can define other value using `autocompleteSearchDebounce` method:

```php
GoogleAutocomplete::make('google_search')
    ->autocompleteSearchDebounce(1000) // 1 second
```

These following **Google API options** can be passed to the `GoogleAutocomplete` field:

### Countries

Add the `countries` method to restrict the countries that should be used for search.

The countries must be passed as a two character ISO 3166-1 Alpha-2 compatible country code. You can find the country codes available at [Wikipedia: List of ISO 3166 country codes](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)

```php
GoogleAutocomplete::make('google_search')
    ->countries([
        'US',
        'AU',
    ])
```

### Language

The language which results should be returned. These are the [supported language codes](https://developers.google.com/maps/faq#languagesupport).

```php
GoogleAutocomplete::make('google_search')
    ->language('pt-BR')
```

### Location

The point around which to retrieve place information as `latitude,longitude`.

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#location) to a detailed description of this option.

### LocationBias

Prefer results in a specified area, by specifying either a radius plus lat/lng, or two lat/lng pairs representing the points of a rectangle. If this parameter is not specified, the API uses IP address biasing by default.

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#locationbias) to a detailed description of this option.

### LocationRestriction

Restrict results to a specified area, by specifying either a radius plus lat/lng, or two lat/lng pairs representing the points of a rectangle.

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#locationrestriction) to a detailed description of this option.

### Offset

The position, in the input term, of the last character that the service uses to match predictions. For example, if the input is Google and the offset is 3, the service will match on Goo. 

### Origin

The origin point as `latitude,longitude` from which to calculate straight-line distance to the destination specified.

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#origin) to a detailed description of this option.

### Radius

The distance in meters within which to return place results.

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#radius) to a detailed description of this option.

### Region

The region code, specified as a [country code top-level domain (ccTLD)](https://en.wikipedia.org/wiki/List_of_Internet_top-level_domains#Country_code_top-level_domains) two-character value. 

```php
GoogleAutocomplete::make('google_search')
    ->region('uk')
```

### SessionToken

Random string which identifies an autocomplete session for billing purposes.

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#sessiontoken) to a detailed description of this option.

### PlaceTypes

Restrict the results to be of a certain type. Pass the [available types](https://developers.google.com/maps/documentation/places/web-service/supported_types) as an array:

```php
GoogleAutocomplete::make('google_search')
    ->placeTypes([
        'lodging',
        'book_store',
        'florist',
    ])
```

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#types) to a detailed description of this option.


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Tapp Network](https://github.com/TappNetwork)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
