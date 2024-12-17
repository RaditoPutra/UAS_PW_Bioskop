<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    // Nonaktifkan penggunaan timestamps (created_at dan updated_at)
    public $timestamps = false;

    protected $fillable = [
        'judul', 'genre', 'durasi', 'poster', 'rating_usia', 'sinopsis', 'trailer',
    ];
}
