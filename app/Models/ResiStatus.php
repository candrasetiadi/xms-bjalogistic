<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResiStatus extends Model
{
    protected $table = 'bja_resi_status';

    public $timestamps = false;

    protected $fillable = ['resi_id', 'status', 'keterangan', 'catatan', 'waktu', 'created_by', 'created_at'];

    protected $casts = ['waktu' => 'datetime', 'created_at' => 'datetime'];

    public function resi()
    {
        return $this->belongsTo(Resi::class, 'resi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
