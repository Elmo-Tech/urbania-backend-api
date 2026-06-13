<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketClient extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;
    
    protected $fillable = [
        'firstname',
        'lastname',
        'company_name',
        'national_number'
    ];

    public function address(): HasMany
    {
        return $this->hasMany(TicketClientAddress::class);
    }

    public function contact(): HasMany
    {
        return $this->hasMany(TicketClientContact::class);
    }

    public function ticket(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

}
