<?php

    namespace App\Policies;

    use App\BuildingSite;
    use App\User;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class BuildingSitePolicy
    {
        use HandlesAuthorization;

        /**
         * Determine whether the user can view the event.
         *
         * @param  \App\User $user
         * @param  \App\BuildingSite $buildingSite
         * @return mixed
         */
        public function view(User $user, BuildingSite $buildingSite)
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
            return ($user->isAdmin() ? true : false);
        }

        /**
         * Determine whether the user can update the event.
         *
         * @param  \App\User $user
         * @param  \App\BuildingSite $buildingSite
         * @return mixed
         */
        public function update(User $user, BuildingSite $buildingSite)
        {
            if ($user->isAdmin()/* and 'closed' != $buildingSite->status*/) {
                return true;
            }

            return false;
        }

        /**
         * Determine whether the user can delete the event.
         *
         * We should check that the admin user that's trying to delete another user is an admin/super admin and that
         * the role permission of the user that's trying to manage other users is lower (meaning higher role) than the
         * record he's trying to delete!
         *
         * @param  \App\User $user
         * @param  \App\BuildingSite $buildingSite
         * @return mixed
         */
        public function delete(User $user, BuildingSite $buildingSite)
        {
            if ($user->isAdmin() and 'closed' != $buildingSite->status) {
                return true;
            }

            return false;
        }

        /**
         * Determine whether the user can restore the event.
         *
         * @param  \App\User $user
         * @param  \App\BuildingSite $buildingSite
         * @return mixed
         */
        public function restore(User $user, BuildingSite $buildingSite)
        {
            //
        }
    }
