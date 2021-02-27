<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    const CREATED_AT    = 'criada_em';
    const UPDATED_AT    = 'alterada_em';

    public $timestamps  = true;
    public $incrementing = false;
    protected $fillable = [
        'titular',
        'cpfcnpj',
        'email',
        'tipo_conta',
        'senha',
        'id',
        'celular',
        'hash'
    ];
    protected $casts = [
        'tipo_conta' => 'integer',
    ];
}
