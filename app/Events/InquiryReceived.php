<?php

namespace App\Events;

use App\Models\Inquiry;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Wird ausgelöst, nachdem eine neue Anfrage dauerhaft gespeichert wurde
 * (erst nach dem Commit der Transaktion). Benachrichtigungen hängen sich
 * in Phase 5 als Listener an dieses Event.
 */
class InquiryReceived implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(public Inquiry $inquiry)
    {
    }
}
