<?php

namespace App;

use App\Model\Issue;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    protected $guard_name = 'web';
    use HasApiTokens, Notifiable , HasRoles;


    public function issue()
    {
        $this->hasMany(Issue::class);
    }
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    protected $fillable = [
        'name', 'email', 'password', 'phone'
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
