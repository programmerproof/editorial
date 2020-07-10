<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function journalist(){
        return $this->belongsTo(Journalist::class);
    }

    public function magazine(){
        return $this->belongsTo(Magazine::class);
    }

    public function magissue(){
        return $this->belongsTo(Magissue::class);
    }

    public function writes(){
        return $this->belongsTo(Writes::class);
    }

    public function sells(){
        return $this->belongsTo(Sells::class);
    }
}
