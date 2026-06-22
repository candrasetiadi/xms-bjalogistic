<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'bja_clients';

    public $incrementing = false;

    protected $keyType = 'integer';

    public $timestamps = false;

    protected $fillable = [
        'id', 'name', 'company', 'phone', 'email',
        'addr', 'city', 'dest', 'note',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'client_id');
    }
}
