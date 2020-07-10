<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    //
    use SoftDeletes;
    
	protected $table = 'state';

    protected $primaryKey = 'code';

    public $timestamps = false;
	
    protected $guarded = ['_token'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function scopeOrderByNameAsc($query)
    {
        return $query->orderBy('name', 'ASC')->get();
    }

    public function scopeWhereStateCode($query, $code)
    {
        return  $query->where('state_code', $code)->OrderByNameAsc();
    }
}
