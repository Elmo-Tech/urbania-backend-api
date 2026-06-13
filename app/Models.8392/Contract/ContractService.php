<?php

namespace App\Models\Contract;

use App\Models\Contract\Service\LavorazioneMainData;
use App\Models\Contract\Service\TecnicaMainData;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractService extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $table = 'contracts_services';

    public function lavorazioneMainData (): HasOne
    {
        return $this->hasOne('App\Models\Contract\Service\LavorazioneMainData');
    }

    public function tecnicaMainData (): HasOne
    {
        return $this->hasOne(TecnicaMainData::class);
    }

}
