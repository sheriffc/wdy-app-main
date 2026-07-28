<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LearnerIdService
{
    /**
     * Max sequence per 2-digit year suffix for a known prefix (a school's emis_id).
     * Uses a range scan rather than LIKE so there's no wildcard-escaping and no
     * ambiguity from hyphens inside the prefix itself. Includes soft-deleted rows
     * deliberately — a human-facing learner_id shouldn't be reissued after a
     * learner record is soft-deleted.
     *
     * @return array<string,int> e.g. ["27" => 3, "26" => 2]
     */
    public static function maxSequencesForPrefix(string $prefix): array
    {
        $rows = DB::table('learner')
            ->where('learner_id', '>=', "$prefix-")
            ->where('learner_id', '<', "$prefix.") // '.' (0x2E) > '-' (0x2D): bounds the "$prefix-" family exactly
            ->pluck('learner_id');

        $max = [];
        $pattern = '/^' . preg_quote($prefix, '/') . '-(\d{2})-(\d{4,})$/';
        foreach ($rows as $id) {
            if (preg_match($pattern, $id, $m)) {
                $year = $m[1];
                $seq = (int) $m[2];
                if (!isset($max[$year]) || $seq > $max[$year]) {
                    $max[$year] = $seq;
                }
            }
        }
        return $max;
    }

    /**
     * Parse an opaque learner_id (as submitted by a client) into its segments.
     * Right-anchored so it fails closed on anything unexpected rather than
     * mis-parsing (the prefix itself may contain hyphens).
     *
     * @return array{prefix:string,year:string,seq:int}|null
     */
    public static function parse(string $learnerId): ?array
    {
        if (!preg_match('/^(.+)-(\d{2})-(\d{4,})$/', $learnerId, $m)) {
            return null;
        }
        return ['prefix' => $m[1], 'year' => $m[2], 'seq' => (int) $m[3]];
    }

    public static function nextAvailable(string $prefix, string $year): int
    {
        $max = self::maxSequencesForPrefix($prefix);
        return ($max[$year] ?? 0) + 1;
    }

    public static function format(string $prefix, string $year, int $seq): string
    {
        return sprintf('%s-%s-%04d', $prefix, $year, $seq);
    }
}
