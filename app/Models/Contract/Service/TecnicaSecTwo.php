<?php

namespace App\Models\Contract\Service;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TecnicaSecTwo extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedBy;

    protected $fillable = [
        'tipologia',
        'data_apozione',
        'data_approvazione',
        'pubblicazione_burl',
        'note',
        'tecnica_main_data_id'
    ];
}
