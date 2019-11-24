<?php

namespace App\Policies;

use App\User;
use App\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any posts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        // Disallow viewing any post
        return false;
    }

    /**
     * Determine whether the user can view the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function view(User $user, Post $post)
    {
        $author = User::findOrFail($post->author);

        // Check if user has blocked the profile or profile has blocked the user
        if (!$user->blockingUser($author->id)) {
            switch (true) {
                // Check if user is the author
                case $user->id === $author->id:

                // Check if post is public
                case !$author->settings['privateAccount']:

                // Check if post is private but user is following author
                case $user->following->where('following_user', $author->id)->first():

                    // Return action allowed
                    return true;
            }
        }

        // Return forbidden by default
        return false;
    }

    /**
     * Determine whether the user can view the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function view_likes(User $user, Post $post)
    {
        $author = User::findOrFail($post->author);

        // Check if user has blocked the profile or profile has blocked the user
        if (!$user->blockingUser($author->id)) {
            switch (true) {
                // Check if user is the author
                case $user->id === $author->id:

                // Check if post likes are public
                case !$author->settings['displayLikes']:

                    // Return action allowed
                    return true;
            }
        }

        // Return forbidden by default
        return false;
    }

    /**
     * Determine whether the user can repost the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function repost(User $user, Post $post)
    {
        $author = User::findOrFail($post->author);

        // Check if user has blocked the profile or profile has blocked the user, and post public status
        return !$user->blockingUser($author->id) && !$author->settings['privateAccount'];
    }

    /**
     * Determine whether the user can create posts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // Return allowed if user exists
        return !!$user->id;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        // Return allowed if user is author
        return $user->id === $post->author;
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        // Return allowed if user is author
        return $user->id === $post->author;
    }
}
