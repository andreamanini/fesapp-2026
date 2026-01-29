<?php

    namespace App\Http\Controllers;

    use App\Customer;
    use App\Report;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;

    class CustomerController extends ApiController
    {
        /**
         * Display a listing of the resource.
         *
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function index(Request $request)
        {
            // Ottieni le date di filtro
            $dateFrom = $request->get('date_from') ? new Carbon($request->get('date_from')) : null;
            $dateTo = $request->get('date_to') ? new Carbon($request->get('date_to')) : null;
            $jsonFormat = $request->get('json_format');
            $searchTerm = $request->input('search'); // Ottieni il termine di ricerca
        
            // Query base
            $customers = Customer::where(function($q) use ($dateFrom, $dateTo, $jsonFormat, $searchTerm) {
                if ($searchTerm) {
                    // Applica il filtro di ricerca
                    $q->search($searchTerm);
                }
            
                if ($dateFrom) {
                    // Logica di filtro data esistente
                    if (!$jsonFormat) {
                        $q->whereHas('reports', function($qq) use ($dateFrom, $dateTo) {
                            $qq->whereBetween('reports.created_at', [
                                $dateFrom->format('Y-m-d 00:00:00'),
                                $dateTo ? $dateTo->format('Y-m-d 23:59:59') : $dateFrom->endOfMonth()->format('Y-m-d 23:59:59')
                            ]);
                        });
                    } else {
                        $q->whereHas('customerReports', function($qq) use ($dateFrom, $dateTo) {
                            $qq->whereBetween('customer_reports.created_at', [
                                $dateFrom->format('Y-m-d 00:00:00'),
                                $dateTo ? $dateTo->format('Y-m-d 23:59:59') : $dateFrom->endOfMonth()->format('Y-m-d 23:59:59')
                            ]);
                        });
                    }
                }
            });
        
            if (!$jsonFormat) {
                $customers = $customers->orderBy('id', 'desc')->paginate();
            
                // Calcola il numero di pagine
                $pages = $customers->total() > 0 && $customers->perPage() > 0 ? ceil($customers->total() / $customers->perPage()) : 0;
            
                $pagination = (object)[
                    'lastPage' => $customers->lastPage(),
                    'perPage' => $customers->perPage(),
                    'currentPage' => $customers->currentPage(),
                    'pages' => $pages
                ];
            
                return view('backend.customers.customers-list', compact('customers', 'pagination', 'dateFrom', 'dateTo'));
            } else {
                return $customers->get();
            }
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
            if ($this->authorize('create', Customer::class)) {
                return view('backend.customers.add-customer');
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
                'company_name' => 'required|unique:customers,company_name',
                'email' => 'nullable|email',
            ]);

            // Validate if the user is authorised to perform this action
            if ($this->authorize('create', Customer::class)) {

                // Insert the customer
                try {
                    $customer = Customer::create([
                        'company_name' => $request->input('company_name'),
                        'email' => $request->input('email'),
                        'vatnumber' => $request->input('vatnumber'),
                        'taxcode' => $request->input('taxcode'),
                        'sdi' => $request->input('sdi'),
                        'email2' => $request->input('email2'),
                        'email3' => $request->input('email3'),
                        'manager' => $request->input('manager'),
                        'telephone' => $request->input('telephone'),
                        'telephone2' => $request->input('telephone2'),
                        'telephone3' => $request->input('telephone3'),
                        'address' => $request->input('address'),
                        'city' => $request->input('city'),
                        'postcode' => $request->input('postcode'),
                        'created_by' => auth()->user()->name . ' ' . auth()->user()->surname,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Errore durante la creazione di un cliente: '. $e->getMessage());
                    abort(500);
                }
            }

            return redirect()->route('customers.index');
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param Customer $customer
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function edit(Customer $customer)
        {
            // Validate if the user is authorised to perform this action
            if ($this->authorize('update', $customer)) {
                return view('backend.customers.add-customer', compact('customer'));
            }
        }

        /**
         * Update the specified resource in storage.
         *
         * @param Request $request
         * @param Customer $customer
         * @return \Illuminate\Http\RedirectResponse
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function update(Request $request, Customer $customer)
        {
            $request->validate([
                'email' => 'nullable|email',
            ]);

            // Validate if the user is authorised to perform this action
            if ($this->authorize('update', $customer)) {

                // Only validate on the company_name if it's changed
                if ($customer->company_name !== $request->input('company_name')) {
                    $request->validate([
                        'company_name' => 'required|unique:customers,company_name',
                    ]);

                    $customer->company_name = $request->input('company_name');
                }

                // Update the customer
                try {
                    $customer->vatnumber = $request->input('vatnumber');
                    
                    $customer->taxcode = $request->input('taxcode');
                    
                    $customer->sdi = $request->input('sdi');
                    
                    $customer->email = $request->input('email');

                    $customer->email2 = $request->input('email2');

                    $customer->email3 = $request->input('email3');

                    $customer->manager = $request->input('manager');

                    $customer->telephone = $request->input('telephone');

                    $customer->telephone2 = $request->input('telephone2');

                    $customer->telephone3 = $request->input('telephone3');

                    $customer->address = $request->input('address');

                    $customer->city = $request->input('city');

                    $customer->postcode = $request->input('postcode');

                    $customer->updated_by = auth()->user()->name . ' ' . auth()->user()->surname;

                    $customer->save();

                } catch (\Exception $e) {
                    Log::error('Errore durante la modifica di un cliente: '. $e->getMessage());
                    abort(500);
                }
            }

            return redirect()->route('customers.index');
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param Customer $customer
         * @return int
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function destroy(Customer $customer)
        {
            // Validate if the user is authorised to perform this action
            if ($this->authorize('delete', $customer)) {

                try {

                    // Check for customer building sites
                    $buildingSites = $customer->buildingSites();
                    if ($buildingSites->count() > 0) {
                        return $this->respondBadRequest('Non è possibile eliminare questo cliente in quanto ha dei cantieri assegnati.');
                    }

                    // Check for customer building sites reports
                    $reports = Report::whereIn('building_site_id', $buildingSites->get()->pluck('id')->toArray());
                    if ($reports->count() > 0) {
                        return $this->respondBadRequest('Non è possibile eliminare questo cliente in quanto ha dei rapportini assegnati.');
                    }

                    // Check for customer building sites customer reports


                    // Delete the customer
                    $customer->delete();

                } catch (\Exception $e) {
                    return $this->respondInternalError('An error has occurred while trying to delete the customer'. $e->getMessage());
                }
            }
        }

        /**
         * @param Request $request
         * @param Customer $customer
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function customerReports(Request $request, Customer $customer)
        {
            return view('backend.reports.customer-charts', compact('customer'));
        }
    }
