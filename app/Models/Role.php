<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the users that belong to the role
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the permissions that belong to the role
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Check if role has a permission
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->where('slug', $permission)->isNotEmpty();
    }

    /**
     * Give permissions to the role
     */
    public function givePermissions(array $permissionIds)
    {
        return $this->permissions()->sync($permissionIds);
    }

    /**
     * Get default role
     */
    public static function getDefaultRole(): ?self
    {
        return static::where('is_default', true)->first();
    }
}
