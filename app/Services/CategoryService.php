<?php

namespace App\Services;

class CategoryService
{
    /**
     * Get preset pagination options.
     */
    public function getPaginationPresets(): array
    {
        return [10, 25, 50, 100];
    }

    /**
     * Get valid per-page value, or default to 25.
     */
    public function getValidPerPage(?int $requested): int
    {
        $presets = $this->getPaginationPresets();

        if ($requested && in_array($requested, $presets)) {
            return $requested;
        }

        return 25;
    }
}
