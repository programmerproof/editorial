<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Magtype extends Model
{
    //
    use SoftDeletes;
    
	protected $table = 'magtype';

    protected $primaryKey = 'id';

    public $timestamps = false;
	
    protected $guarded = ['_token'];

    public function magazines()
    {
        return $this->hasMany(Magazine::class);
    }

    public function scopeOrderByNameAsc($query)
    {
        return $query->orderBy('name', 'ASC')->get();
    }
}
