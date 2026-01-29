<?php

    namespace App\Policies;

    use App\CustomerReport;
    use App\User;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class CustomerReportPolicy
    {
        use HandlesAuthorization;

        /**
         * Determine whether the user can view any models.
         *
         * @param  \App\User $user
         * @return mixed
         */
        public function viewAny(User $user)
        {
            //
        }

        /**
         * Determine whether the user can view the model.
         *
         * @param  \App\User $user
         * @param  \App\CustomerReport $customerReport
         * @return mixed
         */
        public function view(User $user, CustomerReport $customerReport)
        {
            return true;
        }

        /**
         * Determine whether the user can create models.
         *
         * @param  \App\User $user
         * @return mixed
         */
        public function create(User $user)
        {
            return true;
        }

        /**
         * Determine whether the user can update the model.
         *
         * @param  \App\User $user
         * @param  \App\CustomerReport $customerReport
         * @return mixed
         */
        public function update(User $user, CustomerReport $customerReport)
        {
            return ($user->isSuperAdmin() ? true : false);
        }

        /**
         * Determine whether the user can delete the model.
         *
         * @param  \App\User $user
         * @param  \App\CustomerReport $customerReport
         * @return mixed
         */
        public function delete(User $user, CustomerReport $customerReport)
        {
            return ($user->isSuperAdmin() ? true : false);
        }

        /**
         * Determine whether the user can restore the model.
         *
         * @param  \App\User $user
         * @param  \App\CustomerReport $customerReport
         * @return mixed
         */
        public function restore(User $user, CustomerReport $customerReport)
        {
            //
        }

        /**
         * Determine whether the user can permanently delete the model.
         *
         * @param  \App\User $user
         * @param  \App\CustomerReport $customerReport
         * @return mixed
         */
        public function forceDelete(User $user, CustomerReport $customerReport)
        {
            //
        }
    }
