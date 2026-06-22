<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'bja_invoices';

    public $timestamps = false;

    protected $fillable = [
        'num', 'bja_no', 'date', 'due_date', 'tujuan',
        'bill_name', 'bill_phone', 'bill_email', 'bill_addr',
        'ship_name', 'ship_phone', 'ship_addr',
        'rows_json', 'biaya_json', 'calc_mode',
        'sub_total', 'disc', 'total',
        'sales_id', 'client_id', 'order_type',
    ];

    protected $casts = [
        'rows_json'  => 'array',
        'biaya_json' => 'array',
        'date'       => 'date',
        'due_date'   => 'date',
        'sub_total'  => 'decimal:2',
        'disc'       => 'decimal:2',
        'total'      => 'decimal:2',
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
