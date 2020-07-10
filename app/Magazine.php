<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Magazine extends Model
{
    //
    use SoftDeletes;
    
	protected $table = 'magazine';

    protected $primaryKey = 'reg_num';

    public $timestamps = false;
	
    protected $guarded = ['_token'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function magperiodicity()
    {
        return $this->belongsTo(Magperiodicity::class);
    }

    public function magtype()
    {
        return $this->belongsTo(Magtype::class);
    }

    public function magissues(){
        return $this->hasMany(Magissue::class);
    }

    public function writes(){
        return $this->hasMany(Writes::class);
    }

    public function sells(){
        return $this->hasMany(Sells::class);
    }

    public function scopeTitleLike($query, $search)
    {
        return  $query->where('title', 'LIKE', '%'.$search.'%');
    }

    public function scopeOrderByTitleAsc($query)
    {
        return $query->orderBy('title', 'ASC')->get();
    }

    public static function validate($attr)
    {
       //
       $max = Magazine::max('reg_num');
       $validator = \Validator::make(['reg_num' => $attr->reg_num], [
           'reg_num' => 'numeric|max:'.$max
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
