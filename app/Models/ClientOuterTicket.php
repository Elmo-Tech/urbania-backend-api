<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientOuterTicket extends Model
{
    use HasFactory, CreatedUpdatedBy;

    protected $fillable = [
        'firstname',
        'lastname',
        'cf',
        'p_iva',
        'ragione_sociale',
        'address',
        'city',
        'state',
        'email',
        'phone',
        'delegated_firstname',
        'delegated_lastname',
        'delegated_phone',
        'message',
        'anno',
        'email_token',
        'status',
        'service_id',
        'istanza_parameter_id',
        'client_id',
        'delegated_role_id',
        'ticket_client_id',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->number = 'T-' . str_pad($model->id, 6, '0', STR_PAD_LEFT);
            $model->saveQuietly(); // Prevents triggering another event
            $model->created_by = 1;
            $model->updated_by = 1;
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
