<?php

    namespace App\Providers;

    use App\BuildingSite;
    use App\Customer;
    use App\CustomerReport;
    use App\Machinery;
    use App\Media;
    use App\Note;
    use App\Policies\BuildingSitePolicy;
    use App\Policies\CustomerPolicy;
    use App\Policies\CustomerReportPolicy;
    use App\Policies\EmployeePolicy;
    use App\Policies\MachineryPolicy;
    use App\Policies\MediaPolicy;
    use App\Policies\NotePolicy;
    use App\Policies\ReportPolicy;
    use App\Report;
    use App\User;
    use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
    use Illuminate\Support\Facades\Gate;

    class AuthServiceProvider extends ServiceProvider
    {
        /**
         * The policy mappings for the application.
         *
         * @var array
         */
        protected $policies = [
            User::class => EmployeePolicy::class,
            Customer::class => CustomerPolicy::class,
            Machinery::class => MachineryPolicy::class,
            Media::class => MediaPolicy::class,
            BuildingSite::class => BuildingSitePolicy::class,
            Report::class => ReportPolicy::class,
            CustomerReport::class => CustomerReportPolicy::class,
            Note::class => NotePolicy::class,
        ];

        /**
         * Register any authentication / authorization services.
         *
         * @return void
         */
        public function boot()
        {
            $this->registerPolicies();

            //
        }
    }
