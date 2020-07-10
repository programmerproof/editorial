<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jrnspeciality extends Model
{
    //
    use SoftDeletes;
    
	protected $table = 'jrnspeciality';

    protected $primaryKey = 'id';

    public $timestamps = false;
	
    protected $guarded = ['_token'];

    public function journalisties()
    {
        return $this->hasMany(Journalist::class);
    }

    public function scopeOrderByNameAsc($query)
    {
        return $query->orderBy('name', 'ASC')->get();
    }
}
