<?php

namespace App\Models\Contract\Service;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LavorazioneSecTwo extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;
    
    protected $table = 'lavorazione_sec_two';

    protected $fillable = [
        'lavorazione_main_data_id',
        'lavorazione_sec_two_parameter_id',
        'description',
        'years',
        'years_values',
    ];

}
