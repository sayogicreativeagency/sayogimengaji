<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Laporan/pesan dari pengunjung lewat halaman kontak. */
class Laporan extends Model
{
    protected $table = 'laporan';

    protected $guarded = ['id'];
}
