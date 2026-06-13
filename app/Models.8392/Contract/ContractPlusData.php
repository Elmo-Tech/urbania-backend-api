<?php

namespace App\Models\Contract;

use App\Models\Contract;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractPlusData extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $fillable = [
        'contract_parameter_id',
        'contract_id',
        'description',
        'fatturazione'
    ];

    public function contractPlusData(): BelongsTo
    {
        return $this->BelongsTo(Contract::class);
    }


}
