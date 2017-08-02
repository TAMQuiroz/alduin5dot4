<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['name','task_id','url', 'extension'];

    public function task()
    {
        return $this->belongsTo('App\Task','task_id');
    } 
}
