<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminMenu extends Model
{
    use HasFactory;

    protected $table = 'admin_menus';

    protected $fillable = [
        'name',
        'route_name',
        'icon',
        'parent_id',
        'order',
        'is_active',
        'description',
        'required_role',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get parent menu
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(AdminMenu::class, 'parent_id');
    }

    /**
     * Get children menus
     */
    public function children(): HasMany
    {
        return $this->hasMany(AdminMenu::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get only active menus
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get parent menus only
     */
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get menu tree structure
     */
    public static function getMenuTree()
    {
        return static::whereNull('parent_id')->where('is_active', true)->orderBy('order')->with('children')->get();
    }

    /**
     * Get menu tree filtered by user role
     */
    public static function getMenuTreeForRole($role)
    {
        return static::whereNull('parent_id')
            ->where('is_active', true)
            ->where(function ($query) use ($role) {
                $query->whereNull('required_role')
                      ->orWhere('required_role', $role);
            })
            ->orderBy('order')
            ->with(['children' => function ($query) use ($role) {
                $query->where(function ($q) use ($role) {
                    $q->whereNull('required_role')
                      ->orWhere('required_role', $role);
                })->orderBy('order');
            }])
            ->get();
    }

    /**
     * Check if menu is accessible by role
     */
    public function isAccessibleByRole($role): bool
    {
        if ($this->required_role === null) {
            return true;
        }
        return $this->required_role === $role;
    }
}
