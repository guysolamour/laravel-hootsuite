<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateHootsuiteSettingsTable extends SettingsMigration
{
    public function up(): void
    {
        $settings_group  = config('hootsuite.settings.group');
        $settings_fields = config('hootsuite.settings.fields');

        foreach ($settings_fields as $group => $field) {

            if (!is_array($field)) continue;

            foreach ($field as $key => $value) {

                if (!array_key_exists('name', $value)){
                    throw new \Exception("The field [{$key}] in group [{$group}] must have a name");
                }

                $name          = "{$settings_group}.{$value['name']}";
                $default_value = $value['default_value'] ?? null;
                $encrypted     = $value['encrypted'] ?? true;

                if ($encrypted) {
                    $this->migrator->addEncrypted($name, $default_value);
                } else {
                    $this->migrator->add($name, $default_value);
                }
            }
        }

    }


    public function down(): void
    {
        $settings_group  = config('hootsuite.settings.group');
        $settings_fields = config('hootsuite.settings.fields');

        foreach ($settings_fields as $group => $field) {

            if (!is_array($field)) continue;

            foreach ($field as $key => $value) {

                if (!array_key_exists('name', $value)) {
                    throw new \Exception("The field [{$key}] in group [{$group}] must have a name");
                }

                $this->migrator->delete("{$settings_group}.{$value['name']}");
            }
        }
    }
}
