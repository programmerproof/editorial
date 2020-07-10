<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    //
    use SoftDeletes;
    
	protected $table = 'branch';

    protected $primaryKey = 'code';

    public $timestamps = false;
	
    protected $guarded = ['_token'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function sells(){
        return $this->hasMany(Sells::class);
    }
    
    public function scopeNameLike($query, $search)
    {
        return  $query->where('name', 'LIKE', '%'.$search.'%');
    }

    public function scopeOrderByNameAsc($query)
    {
        return $query->orderBy('name', 'ASC')->get();
    }

    public function scopeWhereCityCode($query, $code)
    {
        return  $query->where('city_code', $code)->OrderByNameAsc();
    }

    public function scopeWhere($query, $where, $select)
    {
        return  $query->where($where)->select($select)->get();
    }

    public static function validate($attr)
    {
       //
       $max = Branch::max('code');
       $validator = \Validator::make(['code' => $attr->code], [
           'code' => 'numeric|max:'.$max
         ]);
        
       if ($validator->fails()) {
           $err = $attr->err;
           return $err();
         } else{
               $success = $attr->success;
               return ($success());
           }
    }
}
