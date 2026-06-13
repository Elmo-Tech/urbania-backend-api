<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketClientAddress extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;


    protected $fillable = [
        'ticket_client_id',
        'address',
        'city',
        'state',
        'postal_code',
    ];

    public function clientTicket(): BelongsTo
    {
        return $this->belongsTo(ClientTicket::class);
    }

}
