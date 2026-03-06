<?php

namespace App\Enums;

enum AccessTokenAbility: string
{
    case READ = 'read';
    case WRITE = 'write';
    case DELETE = 'delete';
    case ADMIN = 'admin';

    /**
     * Get the label of the enum
     */
    public function label(): string
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }

    /**
     * Return name-label associative array
     */
    public static function labelArray(): array
    {
        $array = [];
        foreach (self::values() as $value) {
            $array[$value] = self::from($value)->label();
        }

        return $array;
    }

    /**
     * Get the values of each enum in the class
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
