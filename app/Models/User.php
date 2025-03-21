<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{

    protected $fillable = ['name', 'email', 'password', 'phone', 'address', 'role_id'];

    protected $hidden = ['password'];

    public function role()
    {
        return $this->belongsTo(Role::class)->withoutGlobalScopes();
    }
    // Helper method to check role
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
