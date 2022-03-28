<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id', 'email', 'balance','updated_at'
    ];

    public function user()
    {
        return $this->hasOne(User::class,'email','email');

    }
}
