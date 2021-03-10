<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimentacao extends Model
{
    public $timestamps   = false;
    public $incrementing = false;
    protected $table     = 'movimentacoes';
    protected $casts     = [
        'saldo' => 'float',
        'valor' => 'float',
    ];
}
