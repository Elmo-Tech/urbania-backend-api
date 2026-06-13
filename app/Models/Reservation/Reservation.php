<?php

namespace App\Models\Reservation;

use App\Models\Client;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'firstname',
        'lastname',
        'cf',
        'p_iva',
        'ragione_sociale',
        'email',
        'phone',
        'number',
        'delegated_firstname',
        'delegated_lastname',
        'message',
        'date',
        'duration',
        'status',
        'parameter_id',
        'client_id',
        'refuse_reason',
        'confirmation_token'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->number = 'RES-' . str_pad($model->id, 6, '0', STR_PAD_LEFT);
            $model->save(); // Ensure the number is saved
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
