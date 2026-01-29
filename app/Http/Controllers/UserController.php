<?php

    namespace App\Http\Controllers;

    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;

    class UserController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            $employees = User::whereNotNull('created_at')
                ->orderBy('role', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            return view('backend.employees.employee-list', compact('employees'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function create()
        {

            // Validate if the user is authorised to perform this action
            if ($this->authorize('create', User::class)) {
                return view('backend.employees.add-employee');
            }
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|min:2',
                'surname' => 'required|min:2',
                'email' => 'required|unique:users,email',
                'password' => 'required|min:6|confirmed',
                'role' => 'required|in:admin,employee', // TODO: prendere valori da array in user model
            ]);

            // Validate if the user is authorised to perform this action
            if ($this->authorize('create', User::class)) {

                try {
                    $user = User::create([
                        'name' => $request->input('name'),
                        'surname' => $request->input('surname'),
                        'email' => $request->input('email'),
                        'telephone' => $request->input('telephone'),
                        'password' => Hash::make($request->input('password')),
                        'role' => $request->input('role'),
                        'active' => (1 == $request->input('active') ? true : false),
                        'created_by' => auth()->user()->name . ' ' . auth()->user()->surname,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Errore durante la creazione di un utente: '. $e->getMessage());
                    abort(500, $e->getMessage());
                }
            }

            return redirect()->route('employees.index');
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param User $employee
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function edit(User $employee)
        {
            // Validate if the user is authorised to perform this action
            if ($this->authorize('create', $employee)) {
                return view('backend.employees.add-employee', compact('employee'));
            }
        }

        /*
        public function view_sites(User $employee)
        {
            $employee->load('assignedBuildingSites'); // Assicura che i cantieri siano caricati
            echo('<pre>');
            print_r($employee->assignedBuildingSites);
            echo('</pre>');die;
            return view('backend.employees.employee-list-work', compact('employee'));
        }*/

        public function view_sites(User $employee)
        {
            // Query per ottenere i cantieri associati all'utente
            $buildingSites = DB::table('building_site_user')
                ->join('building_sites', 'building_site_user.building_site_id', '=', 'building_sites.id')
                ->leftJoin('customers', 'building_sites.customer_id', '=', 'customers.id')
                ->select('building_sites.*', 'customers.company_name')
                ->where('building_site_user.user_id', $employee->id)
                ->where('building_sites.deleted_at')
                ->get();
        
            // Passiamo i cantieri alla vista
            return view('backend.employees.employee-list-work', compact('employee', 'buildingSites'));
        }



        /**
         * Update the specified resource in storage.
         *
         * @param Request $request
         * @param User $employee
         * @return \Illuminate\Http\RedirectResponse
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function update(Request $request, User $employee)
        {
            $request->validate([
                'name' => 'required|min:2',
                'surname' => 'required|min:2',
                'password' => 'nullable|min:6|confirmed',
                'role' => 'required|in:admin,employee', // TODO: prendere valori da array in user model
            ]);

            // Validate if the user is authorised to perform this action
            if ($this->authorize('create', $employee)) {

                // Validate for user e-mail only if it has been changed
                if ($employee->email != $request->input('email')) {
                    $request->validate([
                        'email' => 'unique:users,email',
                    ]);

                    $employee->email = $request->input('email');
                }

                // Update the user
                try {

                    // Only hash a new password if we actually have a password field filled in...
                    if (null !== $request->input('password')) {
                        $employee->password = Hash::make($request->input('password'));
                    }

                    $employee->name = $request->input('name');

                    $employee->surname = $request->input('surname');

                    $employee->telephone = $request->input('telephone');

                    $employee->role = $request->input('role');

                    $employee->active = (1 == $request->input('active') ? true : false);

                    $employee->updated_by = auth()->user()->name . ' ' . auth()->user()->surname;

                    $employee->save();

                } catch (\Exception $e) {
                    Log::error('Errore durante la modifica di un utente: '. $e->getMessage());
                    abort(500, $e->getMessage());
                }
            }

            return redirect()->route('employees.index');
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param User $employee
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function destroy(User $employee)
        {

            // Validate if the user is authorised to perform this action
            if ($this->authorize('delete', $employee)) {
                try {
                    // Let's make sure we know who has deleted the user (since it's a soft delete row)
                    $employee->update(['updated_by' => auth()->user()->name . ' ' .auth()->user()->surname]);

                    // Delete the user
                    $employee->delete();

                    // IMHO I don't think we should delete anything else except for the user

                } catch (\Exception $e) {
                    Log::error('Errore durante l\'eliminazione di un utente: '. $e->getMessage());
                    die($e);
                }
            }
        }

        public function sites_user(){

            $user = new User();
            $employees = $user->getRoleEmployeeList(false, true);

            return view('backend.employees.employee-list-pre-work', compact('employees'));

        }

    }
