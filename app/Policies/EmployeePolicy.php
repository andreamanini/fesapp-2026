<?php

    namespace App\Policies;

    use App\User;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class EmployeePolicy
    {
        use HandlesAuthorization;

        /**
         * Determine whether the user can view the event.
         *
         * @param  \App\User $user
         * @param  \App\User $employee
         * @return mixed
         */
        public function view(User $user, User $employee)
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
         * @param  \App\User $employee
         * @return mixed
         */
        public function update(User $user, User $employee)
        {
            if ($user->isAdmin() or $employee->id == $user->id) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Determine whether the user can delete the event.
         *
         * We should check that the admin user that's trying to delete another user is an admin/super admin and that
         * the role permission of the user that's trying to manage other users is lower (meaning higher role) than the
         * record he's trying to delete!
         *
         * @param  \App\User $user
         * @param  \App\User $employee
         * @return mixed
         */
        public function delete(User $user, User $employee)
        {
            if ($user->id != $employee->id and $user->isAdmin())
            {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Determine whether the user can restore the event.
         *
         * @param  \App\User $user
         * @param  \App\User $employee
         * @return mixed
         */
        public function restore(User $user, User $employee)
        {
            //
        }
    }
