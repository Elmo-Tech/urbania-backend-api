<?php

namespace App\Models\Contract\Service;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TecnicaSecOne extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $table = 'tecnica_sec_one';
    
    protected $fillable = [
        'description',
        'tecnica_sec_one_parameter_id',
        'tecnica_main_data_id'
    ];

    public function tecnicaMainData(): BelongsTo
    {
        return $this->belongsTo(TecnicaMainData::class);
    }

}
