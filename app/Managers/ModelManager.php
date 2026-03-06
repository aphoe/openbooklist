<?php

namespace App\Managers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModelManager
{
    /**
     * Generate a unique slug for a given database table.
     *
     * @param  string  $table  The database table name.
     * @param  string  $variable  The string to generate the slug from.
     * @param  string  $column  The column name for the slug (default: 'slug').
     */
    public function generateUniqueSlug(string $table, string $variable, string $column = 'slug'): string
    {
        $slug = Str::slug($variable);
        $originalSlug = $slug;

        // Fetch all similar slugs to avoid multiple database queries in a loop
        $existingSlugs = DB::table($table)
            ->where($column, 'LIKE', $slug.'%')
            ->pluck($column)
            ->toArray();

        if (! in_array($slug, $existingSlugs)) {
            return $slug;
        }

        $count = 1;
        while (in_array($slug.'-'.$count, $existingSlugs)) {
            $count++;
        }

        return $slug.'-'.$count;
    }
}
