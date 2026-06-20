<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Models\User;

class SettingRepository
{
    /**
     * Create a new Setting record.
     */
    public function create(User $user, string $setting, string $value): Setting
    {
        $model = new Setting;
        $model->user_id = $user->id;
        $model->setting = $setting;
        $model->value = $value;
        $model->save();

        return $model;
    }

    /**
     * Update an existing Setting record.
     */
    public function update(Setting $setting, string $name, string $value): Setting
    {
        $setting->setting = $name;
        $setting->value = $value;
        $setting->save();

        return $setting;
    }

    /**
     * Delete a Setting record.
     */
    public function delete(Setting $setting): bool
    {
        return (bool) $setting->delete();
    }
}
