<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doa extends Model
{
    protected $table = 'doa';

    public $incrementing = false;

    protected $guarded = [];

    /** Keterangan dari sumber berupa teks biasa: di-escape dulu, lalu alamat rujukannya dijadikan tautan. */
    public function tentangHtml(): string
    {
        return preg_replace('~https?://[^\s<]+~', '<a href="$0" rel="nofollow noopener" target="_blank">$0</a>', e($this->tentang));
    }
}
