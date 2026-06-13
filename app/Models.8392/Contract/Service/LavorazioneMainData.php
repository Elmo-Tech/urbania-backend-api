<?php

namespace App\Models\Contract\Service;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Contract\ContractSportello;


class LavorazioneMainData extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $fillable = [
        'contract_service_id',
        'note_sec_one',
        'note_sec_two',
        'note_sec_three',
        'note_sec_four',
        'imposta',
        'note',
        'n_avvisi',
        'importa',
        'anno_ennissone',
        'anno_accenfamenfo'
    ];

    public function lavorazioneSecOne(): HasMany
    {
        return $this->hasMany(LavorazioneSecOne::class);
    }

    public function lavorazioneSecTwo(): HasMany
    {
        return $this->hasMany(LavorazioneSecTwo::class);
    }

    public function lavorazioneSecThree(): HasMany
    {
        return $this->hasMany(LavorzioneSecThree::class);
    }
    
    public function contractSportello(): HasMany
    {
        return $this->hasMany(ContractSportello::class);
    }


}
