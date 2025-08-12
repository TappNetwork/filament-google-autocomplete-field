# Filament Google Autcomplete Field

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tapp/filament-google-autocomplete-field.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-google-autocomplete-field)
![GitHub Tests Action Status](https://github.com/TappNetwork/filament-google-autocomplete-field/actions/workflows/run-tests.yml/badge.svg)
![GitHub Code Style Action Status](https://github.com/TappNetwork/filament-google-autocomplete-field/actions/workflows/fix-php-code-style-issues.yml/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/tapp/filament-google-autocomplete-field.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-google-autocomplete-field)

This plugin provides an address autocomplete using [Google Place autocomplete API](https://developers.google.com/maps/documentation/places/web-service/autocomplete), with fully customizable address fields.

> [!NOTE]
> The [Google Places API package](https://github.com/SachinAgarwal1337/google-places-api) is used to make API requests to Google Places.

## Version Compatibility

 Filament | Filament Google Autocomplete Field
:---------|:----------------------------------
 3.x      | 1.x
 4.x      | 4.x

## Installation

You can install the package via Composer:

### For Filament 3

```bash
composer require tapp/filament-google-autocomplete-field:"^1.0"
```

### For Filament 4

```bash
composer require tapp/filament-google-autocomplete-field:"^4.0"
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-google-autocomplete-field-config"
```

This is the contents of the published config file:

```php
return [

    'api-key' => env('GOOGLE_PLACES_API_KEY', ''),
    'verify-ssl' => true,
    'throw-on-errors' => false,

];
```

Optionally, you can publish the translation files with:

```bash
php artisan vendor:publish --tag="filament-google-autocomplete-field-translations"
```

## Setup

On [Google console](https://console.cloud.google.com/apis/dashboard), create an application and enable the Places API.

1. Click on "ENABLE APIS AND SERVICES"
2. Search for "Places api"
3. Click to enable it
4. Get the API key

In your Laravel application, add the Google API key to `GOOGLE_PLACES_API_KEY` in your `.env` file:

```php
GOOGLE_PLACES_API_KEY=your_google_place_api_key_here
```

## Appareance

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete02.png)

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete03.png)

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete04.png)

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete05.png)

![Filament Google Autcomplete Field](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/autocomplete06.png)

## Usage

Add the `GoogleAutocomplete` field to your form:

```php
use Tapp\FilamentGoogleAutocomplete\Forms\Components\GoogleAutocomplete;

GoogleAutocomplete::make('google_search'),
```

This will use the default address fields and Google API options. You can also customize the address fields and Google Place API options. See all the options available on [`Available options`](#available-options) item below. For example:

```php
use Tapp\FilamentGoogleAutocomplete\Forms\Components\GoogleAutocomplete;

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

### Using form layouts

The Google autocomplete fields can be wrapped in a Form layout like `Fieldset` or `Section`:

```php
Forms\Components\Fieldset::make('Google Search')
    ->schema([
        GoogleAutocomplete::make('google_search_field')
        // ...
    ]),
```

![Fieldset Layout](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/fieldset_layout.png)

```php
Forms\Components\Section::make('Google Search')
    ->schema([
        GoogleAutocomplete::make('google_search_field')
        // ...
    ]),
```

![Section Layout](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/section_layout.png)

## Places API (original) and Places API (New)

Both the **[Places API (original)](https://developers.google.com/maps/documentation/places/web-service/autocomplete)** and the **[Places API (New)](https://developers.google.com/maps/documentation/places/web-service/place-autocomplete)** are supported.
By default, the Places API (original) it's used. To use the Places API (New) instead, add the `->placesApiNew()` method, like so:

```php
GoogleAutocomplete::make('google_search')
    ->placesApiNew()
```

## Available Options

### Autocompleted Fields

You can use the `withFields` method to define the fields that will be autocompleted.

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

You can override these default fields by passing an array of Filament form fields to `withFields` method:

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

If your database field have a different name than the Google field (for example you DB field is `zip` and you want to use the Google's `postal_code` value returned by API), you can tie the API field to the DB field by passing your DB field name to `'data-google-field'` on `extraInputAttributes` method like so: 

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

Google's Places API returns `long_name` and `short_name` values for address fields. You can choose which one to display by passing it to the `'data-google-value'` on `extraInputAttributes` method: 

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

### Autocomplete Label

The label of the autocomplete select field can be modified using the `->autocompleteLabel()` method:

```php
GoogleAutocomplete::make('google_search')
    ->autocompleteLabel('Select a location')
```

### Autocomplete Placeholder

The placeholder can be modified using the `->autocompletePlaceholder()` method:

```php
GoogleAutocomplete::make('google_search')
    ->autocompletePlaceholder('Select a location')
```

Example with modified `label`, `autocompleteLabel`, and `autocompletePlaceholder`:

```php
GoogleAutocomplete::make('google_search')
    ->autocompleteLabel('Select a location')
    ->autocompletePlaceholder('Click here to search')
    ->label('Searching on Google...')
    ->countries([
        'us',
        'au',
    ])
    ->placeTypes([
        'book_store',
        'cafe',
    ])
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

![Example with modified label, autocompleteLabel, and autocompletePlaceholder](https://raw.githubusercontent.com/TappNetwork/filament-google-autocomplete-field/main/docs/label_placeholder.jpg)

## Google API Options

These following **Google API options** can be passed to the `GoogleAutocomplete` field:

### OPTIONS FOR BOTH APIs

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

Please refer to the [Google Places API original documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#types) and [Google Places API New documentation](https://developers.google.com/maps/documentation/places/web-service/place-autocomplete#includedPrimaryTypes) to a detailed description of this option.

### Language

The language which results should be returned. These are the [supported language codes](https://developers.google.com/maps/faq#languagesupport).

```php
GoogleAutocomplete::make('google_search')
    ->language('pt-BR')
```

### Offset

The position, in the input term, of the last character that the service uses to match predictions. For example, if the input is Google and the offset is 3, the service will match on Goo. 

```php
GoogleAutocomplete::make('google_search')
    ->offset(5)
```

### LocationBias

Prefer results in a specified area, by specifying either a radius plus lat/lng, or two lat/lng pairs representing the points of a rectangle. If this parameter is not specified, the API uses IP address biasing by default.

Please refer to the [Google Places API original documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#locationbias) and [Google Places API New](https://developers.google.com/maps/documentation/places/web-service/place-autocomplete#location-bias-restriction) to a detailed description of this option.

```php
GoogleAutocomplete::make('google_search')
    ->locationBias(
        [
            "circle" => [
                "center" => [
                    "latitude" => 37.7937,
                    "longitude" => -122.3965
                ],
                "radius" => 500.0
            ]
        ]
    )
```

### LocationRestriction

Restrict results to a specified area, by specifying either a radius plus lat/lng, or two lat/lng pairs representing the points of a rectangle.

Please refer to the [Google Places API original documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#locationrestriction) and [Google Places API New](https://developers.google.com/maps/documentation/places/web-service/place-autocomplete#location-bias-restriction) to a detailed description of this option.

### Origin

The origin point as `latitude,longitude` from which to calculate straight-line distance to the destination specified.

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#origin) to a detailed description of this option.

```php
GoogleAutocomplete::make('google_search')
    ->origin(40.7585862,-73.9858202)
```

### Region

The region code used to format the response, specified as a [country code top-level domain (ccTLD)](https://en.wikipedia.org/wiki/List_of_Internet_top-level_domains#Country_code_top-level_domains) two-character value. 

```php
GoogleAutocomplete::make('google_search')
    ->region('uk')
```

### SessionToken

Random string which identifies an autocomplete session for billing purposes.

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#sessiontoken) to a detailed description of this option.


### OPTIONS ONLY FOR PLACES API (ORIGINAL)

### Countries

Add the `countries` method to restrict the countries that should be used for autocomplete search.

The countries must be passed as a two character ISO 3166-1 Alpha-2 compatible country code. You can find the country codes available at [Wikipedia: List of ISO 3166 country codes](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2).

```php
GoogleAutocomplete::make('google_search')
    ->countries([
        'US',
        'AU',
    ])
```

### Location

The point around which to retrieve place information as `latitude,longitude`.

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#location) to a detailed description of this option.

```php
GoogleAutocomplete::make('google_search')
    ->location(40.7585862,-73.9858202)
```

### Radius

The distance in meters within which to return place results.

Please refer to the [Google documentation](https://developers.google.com/maps/documentation/places/web-service/autocomplete#radius) to a detailed description of this option.

```php
GoogleAutocomplete::make('google_search')
    ->radius(10)
```


### OPTIONS ONLY FOR PLACES API (NEW)

### IncludePureServiceAreaBusinesses

`true` - includes businesses that visit or deliver to customers directly, but don't have a physical business location.
`false` - returns only businesses with a physical business location.

```php
GoogleAutocomplete::make('google_search')
    ->includePureServiceAreaBusinesses(true)
```

### IncludedRegionCodes

Only include results from the list of specified regions, specified as an array of up to 15 ccTLD ("top-level domain") two-character values. When omitted, no restrictions are applied to the response.

```php
GoogleAutocomplete::make('google_search')
    ->includedRegionCodes([
        "de", 
        "fr",
    ])
```


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
