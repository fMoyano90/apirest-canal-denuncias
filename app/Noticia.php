<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $fillable = [
        'categoria', 'titulo', 'cuerpo', 'imagen', 'principal'
    ];
}
