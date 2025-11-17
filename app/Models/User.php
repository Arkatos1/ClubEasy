<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use Xoco70\LaravelTournaments\Models\Competitor;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoleAndPermission;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'avatar',
    ];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function competitors()
    {
        return $this->hasMany(Competitor::class, 'user_id');
    }

    public function hasMembership()
    {
        return $this->hasRole('member');
    }

        /**
         * Check if user is administrator for Open Admin
         */
        public function isAdministrator()
        {
            return $this->hasRole('administrator');
        }
    /**
     * Open Admin required methods - proper implementation
     */
    public function visible($roles)
    {
        // Only administrators can see Open Admin menus
        return $this->isAdministrator();
    }

    public function inRoles($roles = [])
    {
        // Only administrators have Open Admin roles
        return $this->isAdministrator();
    }

    public function can($abilities, $arguments = [])
    {
        // Only administrators can do anything in Open Admin
        return $this->isAdministrator();
    }

    public function cannot($abilities, $arguments = [])
    {
        // Non-administrators cannot do anything in Open Admin
        return !$this->isAdministrator();
    }

    public function allPermissions()
    {
        // Only administrators have permissions in Open Admin
        return $this->isAdministrator() ? collect(['*']) : collect();
    }
}
