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
    
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($ticket) {
    //         // Retrieve the latest ticket number with table lock
    //         $latestTicket = static::latest()->lockForUpdate()->first();

    //         // Extract the numeric part and increment it
    //         if ($latestTicket) {
    //             $latestTicketNumber = explode('_', $latestTicket->ticket_number)[0];
    //             $nextTicketNumber = (int)$latestTicketNumber + 1;
    //         } else {
    //             // If no previous ticket exists, start from 1
    //             $nextTicketNumber = 1;
    //         }

    //         // Generate the new ticket number
    //         $ticket->ticket_number = $nextTicketNumber . '_' . date('Y');
    //     });
    // }


}
