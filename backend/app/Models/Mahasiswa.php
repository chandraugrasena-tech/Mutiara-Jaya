<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswas';
    protected $fillable = ['nama'];

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }
}