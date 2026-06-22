<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'bja_quotations';

    public $timestamps = false;

    protected $fillable = [
        'num', 'date', 'perihal', 'lampiran', 'to_name',
        'intro', 'lead_in', 'closing', 'rows_json',
        'sales_id', 'client_id',
    ];

    protected $casts = [
        'rows_json' => 'array',
        'date'      => 'date',
    ];

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
