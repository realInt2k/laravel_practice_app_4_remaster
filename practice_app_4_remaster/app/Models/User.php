<?php

namespace App\Models;

use App\Http\Traits\UserTraits\ChecksUserMeta;
use App\Http\Traits\UserTraits\GetsUserMeta;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use SetsUserMeta;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $phone
 * @property string|null $location
 * @property string|null $about
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAbout($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLocation($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePermissionName(?string $name)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRoleName(?string $name)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User withRolesAndPermissions()
 * @mixin Builder
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, ChecksUserMeta, GetsUserMeta, SetsUserMeta;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'location',
        'phone',
        'about',
        'password_confirmation',
        'email_verified_at'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission', 'user_id', 'permission_id');
    }

    public function scopeWithRolesAndPermissions(Builder $query): Builder|null
    {
        return $query->with('roles')->with('permissions');
    }

    public function scopeWherePermissionName(Builder $query, string|null $name): Builder|null
    {
        return $name ?
            $query->whereRelation('permissions', 'name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereRoleName(Builder $query, string|null $name): Builder|null
    {
        return $name ?
            $query->whereRelation('roles', 'name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereId(Builder $query, int|null $id): Builder|null
    {
        return $id ? $query->where('id', $id) : null;
    }

    public function scopeWhereName(Builder $query, string|null $name): Builder|null
    {
        return $name ? $query->where('name', 'like', '%' . $name . '%') : null;
    }

    public function scopeWhereEmail(Builder $query, string|null $email): Builder|null
    {
        return $email ? $query->where('email', 'like', '%' . $email . '%') : null;
    }
}
