<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    public $timestamps = false;
    protected $table  = 'postlikes';
}
