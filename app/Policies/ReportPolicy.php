<?php

    namespace App\Policies;

    use App\Report;
    use App\User;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class ReportPolicy
    {
        use HandlesAuthorization;

        /**
         * Determine whether the user can view the event.
         *
         * @param  \App\User $user
         * @param  \App\Report $report
         * @return mixed
         */
        public function view(User $user, Report $report)
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
            return ('employee' == $user->role or $user->isAdmin() ? true : false);
        }

        /**
         * Determine whether the user can update the event.
         *
         * @param  \App\User $user
         * @param  \App\Report $report
         * @return mixed
         */
        public function update(User $user, Report $report)
        {
            return (($user->isSuperAdmin() or ('incomplete' == $report->report_type and $report->user_id == $user->id) or ($report->user_id == $user->id)) ? true : false);
        }

        /**
         * Determine whether the user can delete the event.
         *
         * We should check that the admin user that's trying to delete another user is an admin/super admin and that
         * the role permission of the user that's trying to manage other users is lower (meaning higher role) than the
         * record he's trying to delete!
         *
         * @param  \App\User $user
         * @param  \App\Report $report
         * @return mixed
         */
        public function delete(User $user, Report $report)
        {
            return ($user->isSuperAdmin() ? true : false);
        }

        /**
         * Determine whether the user can restore the event.
         *
         * @param  \App\User $user
         * @param  \App\Report $report
         * @return mixed
         */
        public function restore(User $user, Report $report)
        {
            //
        }
    }
