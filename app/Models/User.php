<?php

namespace App\Models;

use App\Enums\Role; // Import enum Role
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    public $timestamps = false;
    protected $table = "users";
    protected $primaryKey = "id";

    protected $fillable = [
        'username',
        'email',
        'password',
        'no_telp',
        'role',
    ];
    

    protected $hidden = [
        'password',
    ];

    // Accessor untuk mengonversi role dari string ke enum
    public function getRoleAttribute($value)
    {
        // Pastikan role adalah enum
        return is_string($value) ? Role::from($value) : $value;
    }

    // Mutator untuk mengonversi role dari enum ke string saat menyimpan ke database
    public function setRoleAttribute($value)
    {
        // Pastikan nilai role yang disimpan ke database berupa string
        $this->attributes['role'] = $value instanceof Role ? $value->value : $value;
    }
}
