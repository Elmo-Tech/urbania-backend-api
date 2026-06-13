<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClientAddress extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;


    protected $fillable = [
        'client_id',
        'address',
        'city',
        'state',
        'postal_code',
        'address_type_id',
    ];


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

}
