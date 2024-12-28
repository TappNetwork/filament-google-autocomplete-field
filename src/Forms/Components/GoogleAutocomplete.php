<?php

namespace Tapp\FilamentGoogleAutocomplete\Forms\Components;

use Closure;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use SKAgarwal\GoogleApi\PlacesApi;
use Tapp\FilamentGoogleAutocomplete\Concerns\CanFormatGoogleParams;
use Tapp\FilamentGoogleAutocomplete\Concerns\HasGooglePlaceApi;

class GoogleAutocomplete extends Component
{
    use CanFormatGoogleParams;
    use Concerns\HasName;
    use HasGooglePlaceApi;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.fieldset';

    protected bool|Closure $isRequired = false;

    protected array $params = [];

    public ?array $withFields = [];

    protected string|Closure $autocompleteFieldColumnSpan = 'full';

    protected int|Closure $autocompleteSearchDebounce = 2000; // 2 seconds

    protected array|Closure $addressFieldsColumns = [];

    final public function __construct(string $name)
    {
        $this->name($name);

        $this->googlePlaces = new PlacesApi(config('filament-google-autocomplete-field.api-key'));
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();
        $static->columnSpanFull();

        return $static;
    }

    /**
     * @return array<Component>
     */
    public function getChildComponents(): array
    {
        $components = [];

        $components[] = Forms\Components\Select::make('google_autocomplete')
            ->native(false)
            ->dehydrated(false)
            ->allowHtml()
            ->live()
            ->searchDebounce($this->getAutocompleteSearchDebounce()) // 2 seconds
            ->searchingMessage(__('filament-google-autocomplete-field::filament-google-autocomplete-field.autocomplete.searching.message'))
            ->searchPrompt(__('filament-google-autocomplete-field::filament-google-autocomplete-field.autocomplete.search.prompt'))
            ->searchable()
            ->hint(new HtmlString(Blade::render('<x-filament::loading-indicator class="h5 w-5" wire:loading wire:target="data.google_autocomplete" />')))
            ->columnSpan($this->getAutocompleteFieldColumnSpan())
            ->getSearchResultsUsing(function (string $search): array {
                $result = $this->getPlaceAutocomplete($search);

                if (! empty($result['predictions'])) {
                    $searchResults = $result['predictions']->mapWithKeys(function (array $item, int $key) {
                        return [$item['place_id'] => $item['description']];
                    });

                    return $searchResults->toArray();
                }

                return [];
            })
            ->afterStateUpdated(function (?string $state, Set $set) {
                if ($state === null) {
                    foreach ($this->getWithFields() as $field) {
                        $set($field->getName(), null);
                    }

                    return;
                }

                $data = $this->getPlace($state);

                $googleFields = $this->getFormattedApiResults($data);

                foreach ($this->getWithFields() as $field) {
                    $fieldExtraAttributes = $field->getExtraInputAttributes();

                    $googleFieldName = count($fieldExtraAttributes) > 0 && isset($fieldExtraAttributes['data-google-field']) ? $fieldExtraAttributes['data-google-field'] : $field->getName();

                    $googleFieldValue = count($fieldExtraAttributes) > 0 && isset($fieldExtraAttributes['data-google-value']) ? $fieldExtraAttributes['data-google-value'] : 'long_name';

                    // if the field contains combined values
                    if (str_contains($googleFieldName, '{')) {
                        $value = $this->replaceFieldPlaceholders($googleFieldName, $googleFields, $googleFieldValue);
                    } else {
                        // bc: Fixes issue with Carson City, NV.  No administrative_area_level_2 provided in search result.
                        $value = '';
                        if (isset($googleFields[$googleFieldName][$googleFieldValue])) {
                            $value = $googleFields[$googleFieldName][$googleFieldValue] ?: '';
                        }
                    }

                    $set($field->getName(), $value);
                    $field->callAfterStateUpdated();
                }
            });

        $addressData = Arr::map(
            $this->getWithFields(),
            function (Component $component) {
                return $component;
            }
        );

        $allComponents = array_merge($components, $addressData);

        return [
            Forms\Components\Grid::make($this->getAddressFieldsColumns())
                ->schema(
                    $allComponents
                ),
        ];
    }

    protected function replaceFieldPlaceholders($googleField, $googleFields, $googleFieldValue)
    {
        preg_match_all('/{(.*?)}/', $googleField, $matches);

        foreach ($matches[1] as $item) {
            $valueToReplace = isset($googleFields[$item][$googleFieldValue]) ? $googleFields[$item][$googleFieldValue] : '';

            $googleField = str_ireplace('{'.$item.'}', $valueToReplace, $googleField);
        }

        return $googleField;
    }

    public function withFields(null|array|string|\Closure $fields): static
    {
        $this->withFields = $fields;

        return $this;
    }

    public function getWithFields(): ?array
    {
        if (empty($this->withFields)) {
            return [
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
            ];
        }

        return $this->evaluate($this->withFields);
    }

    public function autocompleteFieldColumnSpan(string|\Closure $autocompleteFieldColumnSpan = 'full'): static
    {
        $this->autocompleteFieldColumnSpan = $autocompleteFieldColumnSpan;

        $this->params['autocompleteFieldColumnSpan'] = $autocompleteFieldColumnSpan;

        return $this;
    }

    public function getAutocompleteFieldColumnSpan(): ?string
    {
        return $this->evaluate($this->autocompleteFieldColumnSpan);
    }

    public function addressFieldsColumns(null|array|string|\Closure $addressFieldsColumns): static
    {
        $this->addressFieldsColumns = $addressFieldsColumns;

        return $this;
    }

    public function getAddressFieldsColumns(): ?array
    {
        if (empty($this->addressFieldsColumns)) {
            return [
                'default' => 1,
                'sm' => 2,
            ];
        }

        return $this->evaluate($this->addressFieldsColumns);
    }

    public function autocompleteSearchDebounce(int|\Closure $autocompleteSearchDebounce = 2000): static
    {
        $this->autocompleteSearchDebounce = $autocompleteSearchDebounce;

        $this->params['autocompleteSearchDebounce'] = $autocompleteSearchDebounce;

        return $this;
    }

    public function getAutocompleteSearchDebounce(): ?int
    {
        return $this->evaluate($this->autocompleteSearchDebounce);
    }
}
