<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    public $timestamps = false;
    protected $table  = 'postcomments';
}
