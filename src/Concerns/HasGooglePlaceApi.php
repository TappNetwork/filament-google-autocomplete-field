<?php

namespace Tapp\FilamentGoogleAutocomplete\Concerns;

use Closure;
use Illuminate\Support\Arr;

trait HasGooglePlaceApi
{
    protected $googlePlaces;

    protected array $googleAddressFieldNames = [
        'street_number',
        'route',
        'locality',
        'administrative_area_level_2',
        'administrative_area_level_1',
        'country',
        'postal_code',
    ];

    protected array $googleAddressExtraFieldNames = [
        'place_id',
        'formatted_phone_number',
        'international_phone_number',
        'formatted_address',
        'name',
        'website',
    ];

    protected string|array|Closure|null $countries = null;

    protected string|Closure|null $language = null;

    protected string|Closure|null $location = null;

    protected string|Closure|null $locationBias = null;

    protected string|Closure|null $locationRestriction = null;

    protected int|Closure|null $offset = null;

    protected string|Closure|null $origin = null;

    protected int|Closure|null $radius = null;

    protected string|Closure|null $region = null;

    protected string|Closure|null $sessionToken = null;

    protected string|array|Closure|null $placeTypes = null;

    protected function getPlaceAutocomplete($search)
    {
        return $this->googlePlaces->placeAutocomplete($search, $this->params);
    }

    protected function getPlace(string $placeId)
    {
        $addressData = $this->googlePlaces->placeDetails($placeId);

        return $addressData;
    }

    protected function getFormattedApiResults($data): array
    {
        $addressComponents = $data['result']['address_components'];

        // array map with keys
        $addressFields = array_merge(...array_map(function ($key, $item) {
            return [
                $item['types'][0] => [
                    'long_name' => $item['long_name'],
                    'short_name' => $item['short_name'],
                ],
            ];
        }, array_keys($addressComponents), $addressComponents));

        $extraFields = $data['result'];

        // array map with keys
        $extraFields = array_merge(...array_map(function ($key, $item) {
            if (in_array($key, $this->googleAddressExtraFieldNames)) {
                return [
                    $key => [
                        'long_name' => $item,
                        'short_name' => $item,
                    ],
                ];
            } else {
                return [];
            }
        }, array_keys($extraFields), $extraFields));

        $latLngFields = $data['result']['geometry']['location'];

        $latLngFields = [
            'latitude' => [
                'long_name' => $latLngFields['lat'],
                'short_name' => $latLngFields['lat'],
            ],
            'longitude' => [
                'long_name' => $latLngFields['lng'],
                'short_name' => $latLngFields['lng'],
            ],
        ];

        return array_merge($addressFields, $extraFields, $latLngFields);
    }

    public function countries(array|string|Closure|null $countries): static
    {
        $countries = Arr::wrap($countries);

        $this->countries = $countries;

        $this->params['components'] = $this->getFormattedCountries($countries);

        return $this;
    }

    public function getCountries(): ?string
    {
        return $this->evaluate($this->countries);
    }

    public function language(string|Closure|null $language): static
    {
        $this->language = $language;

        $this->params['language'] = $language;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->evaluate($this->language);
    }

    public function location(string|Closure|null $location): static
    {
        $this->location = $location;

        $this->params['location'] = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->evaluate($this->location);
    }

    public function locationBias(string|Closure|null $locationBias): static
    {
        $this->locationBias = $locationBias;

        $this->params['locationbias'] = $locationBias;

        return $this;
    }

    public function getLocationBias(): ?string
    {
        return $this->evaluate($this->locationBias);
    }

    public function locationRestriction(string|Closure|null $locationRestriction): static
    {
        $this->locationRestriction = $locationRestriction;

        $this->params['locationrestriction'] = $locationRestriction;

        return $this;
    }

    public function getLocationRestriction(): ?string
    {
        return $this->evaluate($this->locationRestriction);
    }

    public function offset(int|Closure|null $offset): static
    {
        $this->offset = $offset;

        $this->params['offset'] = $offset;

        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->evaluate($this->offset);
    }

    public function origin(string|Closure|null $origin): static
    {
        $this->origin = $origin;

        $this->params['origin'] = $origin;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->evaluate($this->origin);
    }

    public function radius(int|Closure|null $radius): static
    {
        $this->radius = $radius;

        $this->params['radius'] = $radius;

        return $this;
    }

    public function getRadius(): ?int
    {
        return $this->evaluate($this->radius);
    }

    public function region(string|Closure|null $region): static
    {
        $this->region = $region;

        $this->params['region'] = $region;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->evaluate($this->region);
    }

    public function sessionToken(string|Closure|null $sessionToken): static
    {
        $this->sessionToken = $sessionToken;

        $this->params['sessiontoken'] = $sessionToken;

        return $this;
    }

    public function getSessionToken(): ?string
    {
        return $this->evaluate($this->sessionToken);
    }

    public function placeTypes(array|string|Closure|null $placeTypes): static
    {
        $placeTypes = Arr::wrap($placeTypes);

        $this->placeTypes = $placeTypes;

        $this->params['types'] = $this->getFormattedPlaceTypes($placeTypes);

        return $this;
    }

    public function getPlaceTypes(): ?string
    {
        return $this->evaluate($this->placeTypes);
    }
}
