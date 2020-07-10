<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journalist extends Model
{
    //
    use SoftDeletes;
    
	protected $table = 'journalist';

    protected $primaryKey = 'id';

    public $timestamps = false;
	
    protected $guarded = ['_token'];

    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function jrnspeciality()
    {
        return $this->belongsTo(JrnSpeciality::class);
    }

    public function writes(){
        return $this->hasMany(Writes::class);
    }

    public function scopeCardNumLike($query, $search)
    {
        return  $query->where('id_card_num', 'LIKE', '%'.$search.'%');
    }

    public function scopeOrderBySurnameAsc($query)
    {
        return $query->orderBy('surname_1', 'ASC')->get();
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
