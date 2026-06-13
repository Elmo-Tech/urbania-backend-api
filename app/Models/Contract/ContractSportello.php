<?php

namespace App\Models\Contract;

use App\Models\Contract;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractSportello extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;
    
    protected $table = 'contract_sportello';

    protected $fillable = [
        'tipologia_sportello',
        'data_ins',
        'n_one',
        'worker_id',
        'note',
        'lavorazione_main_data_id',
    ];

    public function contractSportello(): BelongsTo
    {
        return $this->BelongsTo(Contract::class);
    }


}
