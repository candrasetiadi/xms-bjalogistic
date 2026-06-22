<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'bja_users';

    public $timestamps = false;

    protected $fillable = ['name', 'username', 'password', 'role', 'color'];

    protected $hidden = ['password'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'sales_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'sales_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
