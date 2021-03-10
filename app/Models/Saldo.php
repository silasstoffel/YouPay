<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    public $timestamps    = false;
    public $incrementing  = false;
    protected $table      = 'saldos';
    protected $primaryKey = 'conta_id';
    protected $casts      = ['saldo' => 'float'];
}
