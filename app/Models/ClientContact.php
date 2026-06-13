<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $fillable = [
        'client_id',
        'firstname',
        'lastname',
        'phone_number',
        'email',
        'role_id'
    ];


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

}
