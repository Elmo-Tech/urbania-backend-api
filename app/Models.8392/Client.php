<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $fillable = [
        'company_name',
        'trade_register',
        'people_number',
        'cf',
        'secret_info',
        'name_acronym'
    ];


    public function addresses(): HasMany
    {
        return $this->hasMany(ClientAddress::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class)->withTimestamps();
    }

    public function getName(){
        return $this->company_name? $this->company_name: $this->firstname  . " " . $this->lastname;
    }


}
