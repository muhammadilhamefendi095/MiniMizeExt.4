<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id', 'meta', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Cara pakai paling simpel dari mana saja di aplikasi:
     *
     *   AuditLog::record('artwork.approved', $artwork);
     *   AuditLog::record('bid.placed', $bid, ['amount' => $bid->amount]);
     *   AuditLog::record('auth.login'); // tanpa subject model
     */
    public static function record(string $action, ?Model $subject = null, array $meta = []): self
    {
        return static::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'meta' => $meta,
            'ip_address' => RequestFacade::ip(),
            'user_agent' => substr((string) RequestFacade::userAgent(), 0, 500),
        ]);
    }
}
