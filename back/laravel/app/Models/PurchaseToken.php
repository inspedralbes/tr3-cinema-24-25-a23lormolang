<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseToken extends Model
{
    protected $fillable = ['email', 'token', 'expires_at'];
}