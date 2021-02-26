<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    public $timestamps  = false;
    protected $fillable = [
        'titular',
        'cpfcnpj',
        'email',
        'tipo_conta',
        'senha',
        'criado_em'
    ];
    protected $casts = [
        'id'         => 'integer',
        'tipo_conta' => 'integer',
    ];
}
