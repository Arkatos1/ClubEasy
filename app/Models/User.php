<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use Xoco70\LaravelTournaments\Models\Competitor;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'summary',
        'dark_mode',
        'digest',
        'locale',
        'payment_reference',
        'payment_status',
        'payment_submitted_at',
        'payment_verified_at',
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
            'payment_submitted_at' => 'datetime',
            'payment_verified_at' => 'datetime',
        ];
    }

    public function competitors()
    {
        return $this->hasMany(Competitor::class, 'user_id');
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

    public function getRoleAttribute()
    {
        // Map your roles to Canvas roles
        if ($this->hasRole('administrator')) {
            return 3; // Canvas admin
        }
        if ($this->hasRole('editor')) {
            return 2; // Canvas editor
        }
        return 1; // Canvas contributor
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole('administrator');
    }

    public function getIsEditorAttribute()
    {
        return $this->hasRole('administrator') || $this->hasRole('editor');
    }

    public function getIsContributorAttribute()
    {
        // Anyone with access is a contributor
        return $this->hasRole('administrator') || $this->hasRole('editor') || $this->hasRole('contributor');
    }
    public function getDefaultAvatarAttribute()
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?d=identicon';
    }
    public function hasMembership()
    {
        return $this->hasRole('member') || $this->hasRole('trainer') || $this->hasRole('administrator');
    }

    public function isAdministrator()
    {
        return $this->hasRole('administrator');
    }

    public function isTrainer()
    {
        return $this->hasRole('trainer') || $this->hasRole('administrator');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function activeMembership()
    {
        return $this->memberships()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    public function hasActiveMembership(): bool
    {
        return !is_null($this->activeMembership());
    }

    public function pendingMembership()
    {
        return $this->memberships()
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    public function hasPendingMembership(): bool
    {
        return !is_null($this->pendingMembership());
    }

    /**
     * Get membership status text attribute
     */
    public function getMembershipStatusTextAttribute()
    {
        if ($this->hasActiveMembership()) {
            return 'Aktivní člen';
        } elseif ($this->hasPendingMembership()) {
            return 'Čeká na schválení';
        } else {
            return 'Neaktivní';
        }
    }

    /**
     * Get membership expiry date attribute
     */
    public function getMembershipExpiryAttribute()
    {
        $membership = $this->activeMembership();
        return $membership ? $membership->expires_at->format('d.m.Y') : '—';
    }

    /**
     * Get membership type attribute
     */
    public function getMembershipTypeAttribute()
    {
        $membership = $this->activeMembership();
        if (!$membership) return '—';

        return match($membership->type) {
            'premium' => 'Prémiové',
            'family' => 'Rodinné',
            'basic' => 'Základní',
            default => $membership->type
        };
    }

}
