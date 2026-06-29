<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPL extends Model
{
    use HasFactory;

    protected $table = 'ppl';

    protected $fillable = [
        'id_pml',
        'nama',
        'email',
        'target',
    ];

    public function pengawas()
    {
        return $this->belongsTo(PML::class, 'id_pml');
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'id_ppl');
    }

    public function snapshot()
    {
        return $this->hasOne(Monitoring::class, 'id_ppl')
            ->latest('waktu_upload');
    }
}
