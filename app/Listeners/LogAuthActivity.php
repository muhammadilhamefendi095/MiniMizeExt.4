<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthActivity
{
    public function handleLogin(Login $event): void
    {
        AuditLog::record('auth.login', $event->user, ['email' => $event->user->email]);
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            AuditLog::record('auth.logout', $event->user, ['email' => $event->user->email]);
        }
    }
}
