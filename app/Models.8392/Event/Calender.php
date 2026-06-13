<?php

namespace App\Models\Event;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calender extends Model
{
    protected $table = 'calenders';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'client_id',
        'ticket_client_id',
        'all_day',
        'group_id',
        'url',
    ];

    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;
}
