<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kata extends Model
{
    protected $table = 'kata';

    public $timestamps = false;

    protected $guarded = ['id'];
}
