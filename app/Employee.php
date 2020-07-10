<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    //
    use SoftDeletes;
    
	protected $table = 'employee';

    protected $primaryKey = 'id';

    public $timestamps = false;
	
    protected $guarded = ['_token'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeCardNumLike($query, $search)
    {
        return  $query->where('id_card_num', 'LIKE', '%'.$search.'%');
    }

    public static function validate($attr)
    {
       //
       $max = Employee::max('id');
       $validator = \Validator::make(['id' => $attr->id], [
           'id' => 'numeric|max:'.$max
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
