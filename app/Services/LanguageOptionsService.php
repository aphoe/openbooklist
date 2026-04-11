<?php

namespace App\Services;

use Symfony\Component\Intl\Languages;

class LanguageOptionsService
{
    /**
     * Get language options keyed by ISO 639 language code.
     *
     * @return array<string, string>
     */
    public function options(string $displayLocale = 'en'): array
    {
        return collect(Languages::getNames($displayLocale))
            ->filter(fn (string $name, string $code): bool => preg_match('/^[a-z]{2,3}$/', $code) === 1)
            ->sort(fn (string $left, string $right): int => strcasecmp($left, $right))
            ->toArray();
    }

    /**
     * Get all supported ISO 639 language codes.
     *
     * @return list<string>
     */
    public function codes(): array
    {
        return array_keys($this->options());
    }
}
