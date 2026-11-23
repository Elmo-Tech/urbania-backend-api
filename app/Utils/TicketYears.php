<?php

namespace App\Utils;

class TicketYears
{
    // 51 four-digit years plus 50 commas fit in the existing VARCHAR(255).
    public const MAX_YEARS = 51;

    public static function normalizeInput(mixed $value): mixed
    {
        if (is_string($value)) {
            $value = trim($value) === '' ? [] : explode(',', $value);
        } elseif (is_int($value)) {
            $value = [$value];
        }

        if (is_array($value)) {
            return array_map(fn ($year) => is_string($year) ? trim($year) : $year, $value);
        }

        // Leave invalid types intact so request validation rejects them.
        return $value;
    }

    public static function toStorage(array|int|string|null $value): string
    {
        // Called after validation: canonicalize numeric strings before deduplication.
        $years = array_map(fn ($year) => (string) (int) $year, self::normalizeInput($value) ?? []);

        return implode(',', array_unique($years));
    }

    public static function fromStorage(?string $value): array
    {
        $years = self::normalizeInput($value) ?? [];

        return array_values(array_unique(array_filter($years, fn ($year) => $year !== '')));
    }
}
