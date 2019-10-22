<?php

namespace App\Policies;

use App\User;
use App\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any comments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        // Disallow viewing any comment
        return false;
    }

    /**
     * Determine whether the user can view the comment.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function view(User $user, Comment $comment)
    {
        // Check if user has blocked the comment author or vice versa
        if (!$user->blockingUser($comment->author)) {
            switch (true) {
                // Check if user
                case $user->id === $comment->author:

                // Check if blocked from post
                case $user->can('view', $comment->post):

                    // Return action allowed
                    return true;
            }
        }

        // Return forbidden by default
        return false;
    }

    /**
     * Determine whether the user can create comments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, Post $post)
    {
        // Check if user is blocked from post
        return $user->can('view', $post);
    }

    /**
     * Determine whether the user can update the comment.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function update(User $user, Comment $comment)
    {
        // Return allowed if user is author
        return $user->id === $comment->author;
    }

    /**
     * Determine whether the user can delete the comment.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function delete(User $user, Comment $comment)
    {
        switch (true) {
            // Check if user is the comment author
            case $user->id === $comment->author:

            // Check if user is the post owner
            case $user->can('delete', $comment->post):

                // Return action allowed
                return true;
        }

        // Return forbidden by default
        return false;
    }
}
