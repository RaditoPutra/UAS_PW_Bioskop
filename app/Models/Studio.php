<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'studio';

    // Tambahkan id_film ke fillable
    protected $fillable = ['nama_studio', 'jumlah_kursi', 'jadwal_tayang', 'id_film'];

    public function film()
    {
        return $this->belongsTo(Film::class, 'id_film');
    }
}
