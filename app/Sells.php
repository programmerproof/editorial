<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sells extends Model
{
    //
    use SoftDeletes;
    
	protected $table = 'sells';

    protected $primaryKey = 'id';

    public $timestamps = false;
	
    protected $guarded = ['_token'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function magazine(){
        return $this->belongsTo(Magazine::class);
    }

    public static function validate($attr)
    {
       //
       $max = Writes::max('id');
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
