<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PML extends Model
{
    use HasFactory;

    protected $table = 'pml';

    protected $fillable = [
        'nama',
    ];

    public function pencacah()
    {
        return $this->hasMany(PPL::class, 'id_pml');
    }
}
