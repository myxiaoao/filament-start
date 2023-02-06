<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BasePolicy
{
    use HandlesAuthorization;

    public static string $key;

    public function before(User $user, $ability): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['create-' . static::$key]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param $model
     * @return bool
     */
    public function delete(User $user, $model): bool
    {
        if ($user->hasPermissionTo('delete-' . static::$key)) {
            return true;
        }

        if ($user->hasPermissionTo('delete-own-' . static::$key)) {
            return $user->id === $model->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param $model
     * @return bool
     */
    public function forceDelete(User $user, $model): bool
    {
        if ($user->hasPermissionTo('force-delete-' . static::$key)) {
            return true;
        }

        if ($user->hasPermissionTo('force-delete-own-' . static::$key)) {
            return $user->id === $model->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param $model
     * @return bool
     */
    public function restore(User $user, $model): bool
    {
        if ($user->hasPermissionTo('restore-' . static::$key)) {
            return true;
        }

        if ($user->hasPermissionTo('restore-own-' . static::$key)) {
            return $user->id === $model->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param $model
     * @return bool
     */
    public function update(User $user, $model): bool
    {
        if ($user->hasPermissionTo('update-' . static::$key)) {
            return true;
        }

        if ($user->hasPermissionTo('update-own-' . static::$key)) {
            return $user->id === $model->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param $model
     * @return bool
     */
    public function view(User $user, $model): bool
    {
        if ($user->hasPermissionTo('view-' . static::$key)) {
            return true;
        }

        if ($user->hasPermissionTo('view-own-' . static::$key)) {
            return $user->id === $model->user_id;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-' . static::$key);
    }
}
