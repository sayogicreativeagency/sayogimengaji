<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresBelajar extends Model
{
    protected $table = 'progres_belajar';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['didengar_at' => 'datetime', 'dihafal_at' => 'datetime'];
    }
}
