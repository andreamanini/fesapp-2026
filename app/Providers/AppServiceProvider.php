<?php

    namespace App\Providers;

    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         *
         * @return void
         */
        public function register()
        {
            //
        }

        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot()
        {
            view()->composer(
                'backend.building-sites.add-building-site',
                'App\Http\ViewComposers\BackendViewComposer@createBuildingSite'
            );

            view()->composer(
                'backend.employees.add-employee',
                'App\Http\ViewComposers\BackendViewComposer@createEmployee'
            );

            view()->composer(
                'backend.dashboard',
                'App\Http\ViewComposers\BackendViewComposer@dashboard'
            );

            view()->composer(
                'backend.building-sites.bs-list',
                'App\Http\ViewComposers\BackendViewComposer@buildingSiteList'
            );

            view()->composer(
                'backend.reports.daily-employee-report',
                'App\Http\ViewComposers\BackendViewComposer@dailyEmployeeReport'
            );

            view()->composer(
                'backend.reports.create-internal-report',
                'App\Http\ViewComposers\BackendViewComposer@internalDailyReport'
            );
        }
    }
