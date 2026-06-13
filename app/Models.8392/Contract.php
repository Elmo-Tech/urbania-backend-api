<?php

namespace App\Models;

use App\Models\Contract\ContractPlusData;
use App\Models\Contract\ContractService;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $fillable = [
        'client_id',
        'company_id',
        'contract_number',
        'start_date',
        'end_date',
        "status",
        'cig',
        'cup',
        'imposta_id',
        'sign_date',
        'note'
    ];

    protected static function boot() {
        parent::boot();
    
        static::deleted(function ($plusData) {
          $plusData->contractPlusData()->delete();
        });
    }


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function contractPlusData(): HasMany
    {
        return $this->hasMany(ContractPlusData::class);
    }
    

    public function service(): BelongsToMany
    {
        return $this->belongsToMany(ParameterValue::class, 'contracts_services', 'contract_id', 'service_id')
            ->as('services')
            ->withPivot('id', 'start_date', 'end_date', 'payment_id', 'account_number', 'note', 'carico_id')
            ->withTimestamps();
    }

    public function contractService(): HasMany{
        return $this->hasMany(ContractService::class);
    }

    public function ticket(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

}