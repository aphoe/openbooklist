<?php

namespace App\Services;

class TagService
{
    /**
     * Get preset pagination options.
     */
    public function getPaginationPresets(): array
    {
        return [15, 30, 60, 120];
    }

    /**
     * Get valid per-page value, or default to 30.
     */
    public function getValidPerPage(?int $requested): int
    {
        $presets = $this->getPaginationPresets();

        if ($requested && in_array($requested, $presets)) {
            return $requested;
        }

        return 30;
    }
}
