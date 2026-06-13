<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParameterValue extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $fillable = [
        'parameter_id',
        'parameter_value',
        'parameter_order',
        'description',
        'internal_code',
        'multiple_select'
    ];


    public function client(): BelongsTo
    {
        return $this->belongsTo(Parameter::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(ParameterValue::class, 'contracts_services', 'service_id', 'contract_id')->withTimestamps();
    }

}
