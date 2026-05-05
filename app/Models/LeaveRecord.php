<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRecord extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'snapshot_name',
        'snapshot_position',
        'snapshot_office',
        'created_by',   // the user who submitted/created this entry
        'as_of_date',
        'el_vl',
        'el_sl',
        'no_pay_vl',
        'no_pay_sl',
        'no_pay_total',
        'no_pay_dates',
        'undertime_hours',
        'undertime_minutes',
        'undertime_dates',
        'remarks',
    ];

    protected $casts = [
        'as_of_date'       => 'date',
        'no_pay_dates'     => 'array',
        'undertime_dates'  => 'array',
        'no_pay_vl'        => 'float',
        'no_pay_sl'        => 'float',
        'no_pay_total'     => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Format an array of date strings into a compact human-readable string.
     * e.g. ["2026-02-14","2026-02-15","2026-02-19"] → "February 14, 15, 19, 2026"
     */
    public static function formatDates(array $dates): string
    {
        if (empty($dates)) return '—';

        // Sort dates chronologically
        sort($dates);

        // Group by year-month, sort each group's days
        $groups = [];
        foreach ($dates as $d) {
            $dt = \Carbon\Carbon::parse($d);
            $key = $dt->format('Y-m');
            $groups[$key][] = $dt->day;
        }

        $parts = [];
        foreach ($groups as $ym => $days) {
            sort($days);
            $dt = \Carbon\Carbon::parse($ym . '-01');

            // Collapse consecutive days into ranges, e.g. [1,2,3,5] → "1–3, 5"
            $segments = [];
            $start = $days[0];
            $prev  = $days[0];

            for ($i = 1; $i < count($days); $i++) {
                if ($days[$i] === $prev + 1) {
                    // Still consecutive — extend the current run
                    $prev = $days[$i];
                } else {
                    // Gap — close the current run
                    $segments[] = $start === $prev ? (string) $start : "{$start}–{$prev}";
                    $start = $prev = $days[$i];
                }
            }
            // Close the last run
            $segments[] = $start === $prev ? (string) $start : "{$start}–{$prev}";

            $parts[] = $dt->format('F') . ' ' . implode(', ', $segments) . ', ' . $dt->year;
        }

        return implode('; ', $parts);
    }

    /**
     * Ensure a reference number is generated and stored for this record.
     * $type should be 'LB' (Certificate) or 'LW' (Without Pay / Export PDF)
     */
    public function ensureReferenceNumber(string $type): void
    {
        $column = $type === 'LB' ? 'reference_no_lb' : 'reference_no_lw';

        // Do not generate if it already exists
        if (!empty($this->{$column})) {
            return;
        }

        $year = date('Y');
        $prefix = "{$type}-{$year}";

        // Find the latest reference number for the current year
        $latest = self::where($column, 'like', "{$prefix}%")
            ->orderBy($column, 'desc')
            ->value($column);

        if ($latest) {
            $numPart = str_replace($prefix, '', $latest);
            $next = intval($numPart) + 1;
        } else {
            $next = 1;
        }

        // Format: [TYPE]-[YEAR][4-DIGIT INCREMENT], e.g., LB-20260001
        $this->{$column} = sprintf("%s%04d", $prefix, $next);
        $this->save();
    }
}
