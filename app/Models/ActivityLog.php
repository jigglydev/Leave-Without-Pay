<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'description', 'meta'];

    protected $casts = ['meta' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quickly log an activity from anywhere.
     * @param string $action  Short machine key, e.g. 'pdf_generated'
     * @param string $description  Human-readable sentence
     * @param array  $meta  Optional extra context
     */
    public static function log(string $action, string $description, array $meta = []): void
    {
        static::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'description' => $description,
            'meta'        => $meta ?: null,
        ]);
    }

    /** Icon and colour for each action type, used in the view. */
    public function badge(): array
    {
        return match ($this->action) {
            'pdf_generated'          => ['color' => 'rose',   'label' => 'PDF',         'icon' => 'document'],
            'record_edited'          => ['color' => 'blue',   'label' => 'Edit',        'icon' => 'pencil'],
            'record_deleted'         => ['color' => 'slate',  'label' => 'Deleted',     'icon' => 'trash'],
            'user_registered'        => ['color' => 'amber',  'label' => 'New User',    'icon' => 'user'],
            'registration_confirmed' => ['color' => 'green',  'label' => 'Confirmed',   'icon' => 'check'],
            'registration_rejected'  => ['color' => 'red',    'label' => 'Rejected',    'icon' => 'x'],
            'role_updated'           => ['color' => 'slate',  'label' => 'Role Change', 'icon' => 'pencil'],
            'org_added'              => ['color' => 'green',  'label' => 'Org Added',   'icon' => 'user'],
            'org_edited'             => ['color' => 'blue',   'label' => 'Org Edited',  'icon' => 'pencil'],
            'org_deleted'            => ['color' => 'red',    'label' => 'Org Deleted', 'icon' => 'trash'],
            default                  => ['color' => 'slate',  'label' => 'Activity',    'icon' => 'info'],
        };
    }
}
