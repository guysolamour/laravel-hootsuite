<?php

namespace Guysolamour\Hootsuite\Settings;

use Spatie\LaravelSettings\Settings;

class HootsuiteSettings extends Settings
{
    public ?string $hootsuite_access_token = null;

    public ?string $hootsuite_refresh_token = null;

    public ?string $hootsuite_token_expires = null;

    public ?string $bitly_api_token = null;


    public static function group(): string
    {
        return config('hootsuite.settings.group', 'hootsuite');
    }


    public function saveTokens(array $properties)
    {
        return $this->fill($properties)->save();
    }



    public static function encrypted(): array
    {

        $encrypted_fields = [];

        foreach (config('hootsuite.settings.fields', []) as $group => $field) {

            if (!is_array($field)) continue;

            foreach ($field as $key => $value) {

                if (!array_key_exists('name', $value)) {
                    throw new \Exception("The field [{$key}] in group [{$group}] must have a name");
                }

                $encrypted     = $value['encrypted'] ?? true;

                if ($encrypted) {
                    $encrypted_fields[] = $value['name'];
                }
            }
        }

        return $encrypted_fields;
    }

    public function get(string $attribute)
    {
        return $this->{$attribute};
    }

}
