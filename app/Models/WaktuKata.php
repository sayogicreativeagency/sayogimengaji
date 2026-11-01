<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaktuKata extends Model
{
    protected $table = 'waktu_kata';

    public $timestamps = false;

    protected $guarded = ['id'];
}
