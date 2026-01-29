<?php

    namespace App\Http\ViewComposers;


    use App\BuildingSite;
    use App\Customer;
    use App\Machinery;
    use App\Report;
    use App\User;
    use Carbon\Carbon;
    use Illuminate\View\View;

    class BackendViewComposer
    {

        /**
         * ContentSectionComposer constructor.
         */
        public function __construct()
        {
        }

        /**
         * Populates the cartQty variable if the user has one or more products in the cart
         *
         * @param View $view
         */
        public function createBuildingSite(View $view)
        {
            $user = new User();
            $employees = $user->getEmployeeList();

            $customers = Customer::all()->sortBy('company_name');

            $machinery = Machinery::where('machine_type', '=', 'vehicle')
                ->orderBy('machine_name', 'asc')
                ->orderBy('machine_number', 'asc')
                ->get();

            $tools = Machinery::where('machine_type', '=', 'tool')
                ->orderBy('machine_name', 'asc')
                ->orderBy('machine_number', 'asc')
                ->get();

            $bs = new BuildingSite();
            $buildingSyteTypes = $bs->getSiteTypes();

            $view->with([
                'employees' => $employees,
                'customers' => $customers,
                'machinery' => $machinery,
                'tools' => $tools,
                'buildingSyteTypes' => $buildingSyteTypes,
            ]);
        }

        /**
         * Populates the create / edit employee admin page
         *
         * @param View $view
         */
        public function createEmployee(View $view)
        {
            $user = new User();

            $view->with([
                'userRoles' => $user->getUserRoles()
            ]);
        }

        /**
         * Populates the variable needed in the dashboard view
         *
         * @param View $view
         */
        public function dashboard(View $view)
        {
            // Get the list off all employees
            $user = new User();
            $employees = $user->getEmployeeList(false, true);

            $view->with([
                'employees' => $employees
            ]);
        }

        /**
         * Populates the daily building site list view file
         *
         * @param View $view
         */
        public function buildingSiteList(View $view)
        {
            // Check if the currently logged in user has some daily report that needs to be compiled
            $reportsCount = Report::where('user_id', '=', auth()->user()->id)
                ->where('report_type', '=', 'incomplete');

            $view->with([
                'showCompileReportWarning' => ($reportsCount->count() > 0 ? true : false),
                'firstIncompleteReport' => ($reportsCount->count() > 0 ? $reportsCount->first() : null)
            ]);
        }

        /**
         * Populates the daily employee report view file
         *
         * @param View $view
         */
        public function dailyEmployeeReport(View $view)
        {
            $user = new User();
            $employees = $user->getEmployeeList(false, true);

            $view->with([
                'employees' => $employees
            ]);
        }

        /**
         * Populates the internal daily employee report view file
         *
         * @param View $view
         */
        public function internalDailyReport(View $view)
        {
            $user = new User();
            $employees = $user->getEmployeeList(true, true);

            $view->with([
                'employees' => $employees
            ]);
        }
    }
