<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        // Disallow viewing any profile
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        // Check if user has blocked the profile or profile has blocked the user
        if (!$user->blockingUser($model->id)) {
            switch (true) {
                // Check if user
                case $user->id === $model->id:

                // Check if profile is public
                case !$model->settings['private_account']:

                // Check if profile is private but user is following profile
                case $user->following->where('following_user', $model->id)->first():

                    // Return action allowed
                    return true;
            }
        }

        // Return forbidden by default
        return false;
    }

    /**
     * Determine whether the user can view the model in restricted form.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view_restricted(User $user, User $model)
    {
        switch (true) {
            // Check if user
            case $user->id === $model->id:

            // Check if profile is public
            case !$model->settings['private_account']:

            // Check if user has blocked the profile
            case !$user->blockingUser($model->id):

                // Return action allowed
                return true;
        }

        // Return forbidden by default
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // Allow creation of new users
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        // Return allowed if user is profile
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function deactivate(User $user, User $model)
    {
        // Return allowed if user is profile
        return $user->id === $model->id;
    }
}
