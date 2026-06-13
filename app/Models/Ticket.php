<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Ticket extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $fillable = [
        'client_id',
        'contract_id',
        'contract_id_2',
        'service_id',
        'worker_id',
        'ticket_client_id',
        'ticket_number',
        'notify_date',
        'after_notify_date',
        'status',
        'closer_id',
        'end_date',
        'connect_type_id',
        'description',
        'esito',
        'note',
        'status_date',
        'anno',
        'tipologia_istanza',
        'segnalazione',
        'urgenza'

    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($ticket) {
            // Get the ID of the newly created ticket record
            $ticketId = $ticket->id;

            // Get the current year
            $currentYear = date('Y');

            // Generate the ticket number based on ID and current year
            $ticketNumber = $ticketId . '_' . $currentYear;

            // Update the ticket with the generated ticket number
            $ticket->ticket_number = $ticketNumber;
            $ticket->save();
        });
    }


}
