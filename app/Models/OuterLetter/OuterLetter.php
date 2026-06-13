<?php

namespace App\Models\OuterLetter;

use App\Models\Client;
use App\Models\ParameterValue;
use App\Models\User;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OuterLetter extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedBy;

    protected $fillable = [
        'letter_id',
        'name',
        'address',
        'cap',
        'city',
        'province',
        'internal_code',
        'client_id',
        'service_id',
        'year',
        'cf',
        'numero'
    ];

    public function clients(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function services(): BelongsTo
    {
        return $this->belongsTo(ParameterValue::class, 'service_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
