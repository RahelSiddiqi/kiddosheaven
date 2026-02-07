<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'group',
    ];

    /**
     * Get the roles that belong to the permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Get permissions grouped by group
     */
    public static function getGroupedPermissions()
    {
        return static::all()->groupBy('group');
    }

    /**
     * Get permissions by group
     */
    public static function getByGroup(string $group)
    {
        return static::where('group', $group)->get();
    }
}
