<?php

    namespace App\Policies;

    use App\Media;
    use App\User;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class MediaPolicy
    {
        use HandlesAuthorization;

        /**
         * Determine whether the user can view the event.
         *
         * @param  \App\User $user
         * @param  \App\Media $media
         * @return mixed
         */
        public function view(User $user, Media $media)
        {
            return true;
        }

        /**
         * Determine whether the user can create events.
         *
         * @param  \App\User $user
         * @return mixed
         */
        public function create(User $user)
        {
            return (in_array($user->role, ['admin', 'employee']) ? true : false);
        }

        /**
         * Determine whether the user can update the event.
         *
         * @param  \App\User $user
         * @param  \App\Media $media
         * @return mixed
         */
        public function update(User $user, Media $media)
        {
            return (in_array($user->role, ['admin', 'employee']) ? true : false);
        }

        /**
         * Determine whether the user can delete the event.
         *
         * We should check that the admin user that's trying to delete another user is an admin/super admin and that
         * the role permission of the user that's trying to manage other users is lower (meaning higher role) than the
         * record he's trying to delete!
         *
         * @param  \App\User $user
         * @param  \App\Media $media
         * @return mixed
         */
        public function delete(User $user, Media $media)
        {
            return ($user->isAdmin() ? true : false);
        }

        /**
         * Handles the permission for image tagging
         *
         * @param User $user
         * @param Media $media
         * @return bool
         */
        public function tagImage(User $user, Media $media)
        {
            return true;
        }

        /**
         * Handles the permission for image tag remove
         *
         * @param User $user
         * @param Media $media
         * @return bool
         */
        public function removeTag(User $user, Media $media)
        {
            return ($user->isAdmin() ? true : false);
        }

        /**
         * Determine whether the user can restore the event.
         *
         * @param  \App\User $user
         * @param  \App\Media $media
         * @return mixed
         */
        public function restore(User $user, Media $media)
        {
            //
        }
    }
