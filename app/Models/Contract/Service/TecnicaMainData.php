<?php

namespace App\Models\Contract\Service;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TecnicaMainData extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $fillable = [
        'note_sec_one',
        'tipologia',
        'data_apozione',
        'data_approvazione',
        'pubblicazione_burl',
        'note',
        'contract_service_id'
    ];

    public function tecnicaSecOne(): HasMany
    {
        return $this->hasMany(TecnicaSecOne::class);
    }

    public function tecnicaSecTwo(): HasMany
    {
        return $this->hasMany(TecnicaSecTwo::class);
    }


}
