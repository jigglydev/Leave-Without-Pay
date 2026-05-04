<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedLetter extends Model
{
    protected $fillable = [
        'reference_no',
        'type',
        'person_id',
        'employee_name',
        'employee_prefix',
        'employee_position',
        'employee_office',
        'month',
        'year',
        'occurrences',
        'certifier_name',
        'certifier_position',
        'created_by',
    ];

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Returns a human-readable label for the letter type.
     */
    public function typeLabel(): string
    {
        return match ($this->type) {
            'tardy'     => 'Tardy',
            'undertime' => 'Undertime',
            default     => ucfirst($this->type),
        };
    }

    /**
     * Ensure a reference number is generated and stored for this record.
     */
    public function ensureReferenceNumber(): void
    {
        if (!empty($this->reference_no)) {
            return;
        }

        $prefix = $this->type === 'tardy' ? 'TR' : 'UN';
        $year = date('Y');
        $searchPrefix = "{$prefix}-{$year}";

        $latest = self::where('reference_no', 'like', "{$searchPrefix}%")
            ->orderBy('reference_no', 'desc')
            ->value('reference_no');

        if ($latest) {
            $numPart = str_replace($searchPrefix, '', $latest);
            $next = intval($numPart) + 1;
        } else {
            $next = 1;
        }

        $this->reference_no = sprintf("%s%04d", $searchPrefix, $next);
        $this->save();
    }
}
