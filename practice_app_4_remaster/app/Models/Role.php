<?php

namespace App\Models;

use App\Http\Traits\ToArrayCorrectTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, ToArrayCorrectTimeZone;
    protected $table = 'roles';
    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    public function hasPermissionId($id)
    {
        return $this->permissions->find($id) !== null;
    }

    public function syncPermissionIds($permissionIds)
    {
        $this->permissions()->sync($permissionIds);
    }

    public function scopeWithPermissions($query)
    {
        return $this->with('permissions');
    }

    public function scopeWherePermissionName($query, $permissionName)
    {
        return $permissionName ?
            $query->whereHas('permissions', fn ($query) => $query->where('name', 'like', '%' . $permissionName . '%')) :
            null;
    }

    public function scopeWhereId($query, $id)
    {
        return $id ? $query->where('id', $id) : null;
    }

    public function scopeWhereName($query, $name)
    {
        return $name ? $query->where('name', 'like', '%' . $name . '%') : null;
    }
}
