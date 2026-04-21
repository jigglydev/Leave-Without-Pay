<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_number',
        'last_name',
        'given_name',
        'middle_name',
        'suffix',
        'office',
        'position',
    ];

    /**
     * "Last Name Suffix, Given Name M." — same format as User::getFullNameAttribute()
     */
    public function getFullNameAttribute(): string
    {
        $mi = $this->middle_name ? strtoupper(substr($this->middle_name, 0, 1)) . '.' : '';
        $parts = array_filter([
            trim($this->last_name . ($this->suffix ? ' ' . $this->suffix : '')),
            trim($this->given_name . ($mi ? ' ' . $mi : '')),
        ]);
        return implode(', ', $parts);
    }

    /**
     * "Given Middle Last Suffix" — for PDF rendering
     */
    public function getPdfNameAttribute(): string
    {
        $parts = array_filter([
            trim($this->given_name),
            trim($this->middle_name ?? ''),
            trim($this->last_name . ($this->suffix ? ' ' . $this->suffix : '')),
        ]);
        return implode(' ', $parts);
    }
}
