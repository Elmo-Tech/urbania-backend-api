<?php

namespace App\Models\Contract\Service;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LavorzioneSecThree extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedBy;

    protected $fillable = [
        'lavorazione_main_data_id',
        'imposta',
        'note',
        'n_avvisi',
        'importa',
        'anno_ennissone',
        'anno_accertamento'
    ];
}
