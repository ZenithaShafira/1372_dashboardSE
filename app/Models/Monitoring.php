<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    use HasFactory;

    protected $table = 'monitoring';

    protected $fillable = [
        'id_ppl',
        'total_progress',
        'waktu_upload'
    ];

    protected $casts = [
        'waktu_upload' => 'datetime'
    ];

    public function pencacah()
    {
        return $this->belongsTo(PPL::class, 'id_ppl');
    }
}