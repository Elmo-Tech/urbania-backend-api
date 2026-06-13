<?php

namespace App\Models\Reservation;

use App\Models\Client;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationSchedule extends Model
{
    use HasFactory, CreatedUpdatedBy, SoftDeletes;

    protected $fillable = [
        'schedule',
        'client_id',
    ];

    protected $casts = [
        'schedule' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
