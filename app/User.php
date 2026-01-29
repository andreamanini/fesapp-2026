<?php

    namespace App;

    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;

    class User extends Authenticatable
    {
        use Notifiable, SoftDeletes;

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'name',
            'surname',
            'email',
            'phone',
            'role',
            'created_by',
            'updated_by',
            'password',
            'deleted_at',
            'work_notified'
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            'password',
            'remember_token',
        ];

        /**
         * The attributes that should be cast to native types.
         *
         * @var array
         */
        protected $casts = [
            'email_verified_at' => 'datetime',
        ];

        protected $adminRoles = [
            'admin',
            'superadmin'
        ];

        protected $userRoles = [
           'admin' => 'Amministratore',
           'employee' => 'Operaio'
        ];

        /**
         * Function used to verify backend access for the user
         *
         * @return bool
         */
        public function checkBackendAccess()
        {
            return in_array($this->role, $this->adminRoles);
        }

        /**
         * Function used to verify if a user is an admin
         *
         * @return bool
         */
        public function isAdmin()
        {
            return in_array($this->role, $this->adminRoles);
        }

        /**
         * Function used to verify if a user is a super admin
         *
         * @return bool
         */
        public function isSuperAdmin()
        {
            return ('superadmin' == $this->role);
        }

        /**
         * Retrieves the user roles array
         *
         * @return array
         */
        public function getUserRoles()
        {
            return $this->userRoles;
        }

        /**
         * Retrieves all the building sites where this user has access
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
         */
        public function buildingSites()
        {
            return $this->belongsToMany(BuildingSite::class, 'building_site_user');
        }

        public function assignedBuildingSites()
        {
            return $this->belongsToMany(BuildingSite::class, 'building_site_user', 'user_id', 'building_site_id');
        }


        /**
         * Function used to verify if the user is allowed to do csv exports
         *
         * @return bool
         */
        public function canCsvExport()
        {
            return (in_array(auth()->user()->email, ['maniniandrea@gmail.com', 'massimo.farinon@fes-servizi.it', 'amministrazione@fes-servizi.it','roger.dicostanzo@fes-servizi.it','daniele.plebani@assyrus.it']));
        }

        /**
         * Function used to verify if the user is allowed to do SAL exports
         *
         * @return bool
         */
        public function canGenerateSal()
        {
            return (in_array(auth()->user()->email, ['maniniandrea@gmail.com', 'massimo.farinon@fes-servizi.it', 'amministrazione@fes-servizi.it']));
        }


        /**
         * Retrieves a list of employees
         *
         * @param bool $excludeLoggedInUser
         * @param bool $orderByNameSurname
         * @return mixed
         */
        public function getEmployeeList(bool $excludeLoggedInUser = false, bool $orderByNameSurname = false)
        {
            $query = $this->where(function($q) {
                $q->where('role', '=', 'employee')
                    ->orWhere('email', '=', 'roger.dicostanzo@fes-servizi.it');
            })->where(function($q) use ($excludeLoggedInUser) {
                if ($excludeLoggedInUser) {
                    $q->where('id', '!=', auth()->user()->id);
                }
            });
            
            $query->where('active', '=', 1);

            // Add order by name and surname
            if ($orderByNameSurname) {
                $query->orderBy('name', 'asc')
                    ->orderBy('surname', 'asc');
            }

            return $query->get();
        }

        public function getEmployeeListPlus(bool $excludeLoggedInUser = false, bool $orderByNameSurname = false)
        {
            $query = $this->where(function($q) {
                $q->where('role', '=', 'employee')
                    ->orWhere('email', '=', 'roger.dicostanzo@fes-servizi.it')
                    ->orWhere('email', '=', 'massimo.algisi@fes-servizi.it')
                    ->orWhere('email', '=', 'stefano.sarzi@fes-servizi.it');
            })->where(function($q) use ($excludeLoggedInUser) {
                if ($excludeLoggedInUser) {
                    $q->where('id', '!=', auth()->user()->id);
                }
            });
            
            $query->where('active', '=', 1);

            // Add order by name and surname
            if ($orderByNameSurname) {
                $query->orderBy('name', 'asc')
                    ->orderBy('surname', 'asc');
            }

            return $query->get();
        }

        public function getRoleEmployeeList(bool $excludeLoggedInUser = false, bool $orderByNameSurname = false)
        {//come getEmployeeList ma escludendo roger.dicostanzo@fes-servizi.it
            $query = $this->where(function($q) {
                $q->where('role', '=', 'employee');
            })->where(function($q) use ($excludeLoggedInUser) {
                if ($excludeLoggedInUser) {
                    $q->where('id', '!=', auth()->user()->id);
                }
            });
            
            $query->where('active', '=', 1);

            // Add order by name and surname
            if ($orderByNameSurname) {
                $query->orderBy('name', 'asc')
                    ->orderBy('surname', 'asc');
            }

            return $query->get();
        }
        
        public function getActualEmployeeList($user = false)
        {
            $query = $this->where(function($q) {
                $q->where('role', '=', 'employee');
            });
            $query->where('active', '=', 1);
            $query->leftJoin('works', 'works.user_id', '=', 'users.id');
            $query->orderBy('name', 'asc')->orderBy('surname', 'asc');
            if ($user) $query->where('users.id', '=', $user);
            $query->select('works.*','users.name','users.surname','users.id as table_user_id');

            return $query->get();
        }

    }
