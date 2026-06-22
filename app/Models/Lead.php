<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $table = 'bja_leads';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'date', 'name', 'company', 'phone', 'email',
        'tujuan', 'detail', 'source', 'leads_per_day',
        'klasifikasi', 'status', 'sales_id', 'client_id',
        'note', 'est_value',
    ];

    protected $casts = [
        'date'      => 'date',
        'est_value' => 'decimal:2',
    ];

    const STATUSES = ['belum', 'dihubungi', 'followup', 'deal', 'batal'];
    const KLASIFIKASI = ['Potensial', 'Tidak Potensial', 'Netral'];

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
