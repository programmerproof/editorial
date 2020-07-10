<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    //
    use SoftDeletes;
    
	protected $table = 'city';

    protected $primaryKey = 'code';

    public $timestamps = false;
	
    protected $guarded = ['_token'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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
