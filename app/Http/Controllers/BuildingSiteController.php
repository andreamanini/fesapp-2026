<?php

    namespace App\Http\Controllers;

    use App\BuildingSite;
    use App\BuildingSiteNote;
    use App\CustomerReport;
    use App\Mail\BuildingSiteAssignement;
    use App\Material;
    use App\Media;
    use App\Report;
    use App\User;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Mail;


    class BuildingSiteController extends ApiController
    {
        /**
         * Display a listing of the resource.
         *
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function index(Request $request)
        {
            // Get the filter dates
            $startDate = $request->get('date_from');
            $endDate = $request->get('date_to');
            $buildingSiteIdFilter = $request->get('building_site_id');
            $employeeIdFilter = $request->get('eid');

            // Display all the stats based on the month selected (if any)
            if (null !== $startDate) {
                $sdArray = explode('-', $startDate);
                $dateFrom = new Carbon(date("{$sdArray[2]}-{$sdArray[1]}-{$sdArray[0]} 00:00:00"));
            } else {
                $dateFrom = new Carbon(date("Y-m-01 00:00:00"));
            }


            if (null !== $endDate) {
                $edArray = explode('-', $endDate);
                $dateTo = new Carbon(date("{$edArray[2]}-{$edArray[1]}-{$edArray[0]} 23:59:59"));
            } else {
                $dateTo = new Carbon(date("Y-m-t 23:59:59"));
            }
            
            
            $jsonFormat = $request->get('json_format');
            
            // Lista dei dipendenti
            $user = new User();
            $employees = $user->getEmployeeList(false, true);
            
            $buildingSites = BuildingSite::whereNotNull('building_sites.created_at')
                ->where(function($q) use ($request, $jsonFormat, $dateFrom, $dateTo) {
                    if ('true' !== $request->get('show_closed')) {
                        $q->where('status', 'open');
                    }

                    // If the request has a customer_id parameter we should filter the building sites by customer
                    if (null !== $request->get('customer_id')) {
                        $q->where('customer_id', '=', $request->get('customer_id'));
                    }
                    
                    if (null !== $dateFrom AND null !== $dateTo AND null === $jsonFormat) {
                        //$q->where('building_sites.created_at', '>=', $dateFrom->format('Y-m-d'));
                        //$q->where('building_sites.created_at', '<=', $dateTo->format('Y-m-d'));
                        $q->whereBetween(
                            'building_sites.created_at',
                            [
                                $dateFrom->format('Y-m-d 00:00:00'),
                                (null == $dateTo ? $dateFrom->endOfMonth()->format('Y-m-d 23:59:59') : $dateTo->format('Y-m-d 23:59:59'))
                            ]
                        );
                        $q->orwhereBetween(
                            'customer_reports.created_at',
                            [
                                $dateFrom->format('Y-m-d 00:00:00'),
                                (null == $dateTo ? $dateFrom->endOfMonth()->format('Y-m-d 23:59:59') : $dateTo->format('Y-m-d 23:59:59'))
                            ]
                        );
                        $q->orwhereBetween(
                            'reports.created_at',
                            [
                                $dateFrom->format('Y-m-d 00:00:00'),
                                (null == $dateTo ? $dateFrom->endOfMonth()->format('Y-m-d 23:59:59') : $dateTo->format('Y-m-d 23:59:59'))
                            ]
                        );
                        
                    }                    
                    

                    // Get only those building sites where the admin has created a report for in the given month
                    /*
                    if (null !== $jsonFormat and null !== $dateFrom) {
                        $q->whereHas('customerReports', function($qq) use ($dateFrom, $dateTo) {
                            $qq->whereBetween(
                                'customer_reports.created_at',
                                [
                                    $dateFrom->format('Y-m-d 00:00:00'),
                                    (null == $dateTo ? $dateFrom->endOfMonth()->format('Y-m-d 23:59:59') : $dateTo->format('Y-m-d 23:59:59'))
                                ]
                            );
                        });
                    }*/

                })
                ->where(function($q) use ($employeeIdFilter,$buildingSiteIdFilter){
                    // If the user is not an admin he should only see building sites where he is assigned to
                    if (!auth()->user()->isAdmin()) {
                        $q->whereHas('employees', function($qq) {
                            $qq->where('users.id', '=', auth()->user()->id);
                        })->orWhere('manager_id', '=', auth()->user()->id);
                    } else {
                        if (null !== $employeeIdFilter) {
                            $q->whereHas('employees', function($qq) use ($employeeIdFilter,$buildingSiteIdFilter){
                                if (null !== $employeeIdFilter) {
                                    $qq->where('users.id', '=', $employeeIdFilter);
                                }
                            });
                        }
                        if (null !== $buildingSiteIdFilter) {
                            $q->where('building_sites.id', $buildingSiteIdFilter);
                        }
                    }

                })
                ->leftJoin('reports' ,function($join){
                    $join->on('reports.building_site_id', '=', 'building_sites.id')->where('reports.deleted_at');
                })
                ->leftJoin('customer_reports', 'customer_reports.building_site_id', '=', 'building_sites.id')
                ->orderBy('building_sites.id', 'desc')
                ->select('building_sites.*') 
                ->distinct('building_sites.*')
                ->get();

                
            //controllo se l'utente ha già visualizzato la notifica
            $user = auth()->user();
            $showWorkNotification = false;
            if (!$user->work_notified) {
                // Se non ha visualizzato, mostra la notifica e aggiorna il flag
                $showWorkNotification = true;
        
                // Imposta il flag per indicare che ha visualizzato la notifica
                $user->work_notified = true;
                $user->save();
            }

            if (null !== $jsonFormat) {
                return $buildingSites;
            } else {
                return view('backend.building-sites.bs-list', compact('buildingSites','dateFrom','dateTo','employeeIdFilter','employees','buildingSiteIdFilter','showWorkNotification'));
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
            if ($this->authorize('create', BuildingSite::class)) {
                return view('backend.building-sites.add-building-site');
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
//                'site_name' => 'required|unique:building_sites,site_name',
                'manager_id' => 'nullable|exists:users,id',
                'customer_id' => 'required|exists:customers,id',
                'machines' => 'nullable|array',
            ]);


            // Validate if the user is authorised to perform this action
            if ($this->authorize('create', BuildingSite::class)) {

                try {
                    // Form a json version of the address
                    $address = [
                        'lat' => $request->input('lat'),
                        'lng' => $request->input('lng'),
                        'street_number' => $request->input('street_number'),
                        'route' => $request->input('route'),
                        'locality' => $request->input('locality'),
                        'administrative_area_level_1' => $request->input('administrative_area_level_1'),
                        'country' => $request->input('country'),
                        'postal_code' => $request->input('postal_code'),
                        'autocomplete' => $request->input('address')
                    ];

                    // Create a json array of the materials
                    $materials = [
                        'materials' => $request->input('materials'),
                        'qty' => $request->input('material_qty'),
                        'base' => $request->input('base'),
                    ];

                    // Add new materials to the materials table
                    foreach($request->input('materials') as $material) {
                        if (null !== $material) {
                            $materialName = strtolower($material);
                            $exists = DB::table('materials')
                            ->whereRaw("LOWER(material_name) = '".addslashes($materialName)."'")
                                ->count();

                            if ($exists == 0) {
                                Material::create([
                                    'material_name' => $material
                                ]);
                            }
                        }
                    }

                    // Formatting the quote_date field
                    if (null !== $request->input('quote_date')) {
                        $quoteDateArray = explode('/', $request->input('quote_date'));
                        $quoteDate = $quoteDateArray[2] . '-' . $quoteDateArray[1] . '-' . $quoteDateArray[0];
                    }

                    // Create the building site record
                    $bs = BuildingSite::create([
                        'site_name' => $request->input('site_name'),
                        'manager_id' => $request->input('manager_id'),
                        'customer_id' => $request->input('customer_id'),
                        //'notes' => $request->input('notes'),
                        'customer_notes' => $request->input('customer_notes'),
                        'site_type' => (null !== $request->input('site_type') ? json_encode($request->input('site_type')) : null),
                        'address' => json_encode($address),
                        'materials' => json_encode($materials),
                        'quote_number' => $request->input('quote_number'),
                        'quote_date' => (isset($quoteDate) ? $quoteDate : null),
                        'order_number' => $request->input('order_number'),
                        'created_by' => auth()->user()->name . ' ' . auth()->user()->surname,
                    ]);

                    // Store the selected machines in the bs-machinery middle table
                    $bs->machineries()
                        ->attach($request->input('machines'), [
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $bs->created_by,
                        ]);

                    // Update the employees that should have visibility on this building site
                    $bs->employees()
                        ->sync($request->input('employee_visibility'));

                    // Update the building site notes
                    if (null !== $request->input('note_body')) {
                        for($n=0; $n<count($request->input('note_body')); $n++) {

                            if (null !== $request->input('note_body')[$n]) {

                                $ndArray = (null !== $request->input('note_date')[$n] ? explode('/',$request->input('note_date')[$n]) : null);
                                $noteDate = (null !== $ndArray ? $ndArray[2] . '-' . $ndArray[1] . '-' . $ndArray[0] : null);

                                BuildingSiteNote::create([
                                    'note_title' => $request->input('note_title')[$n],
                                    'note_body' => $request->input('note_body')[$n],
                                    'note_date' => $noteDate,
                                    'building_site_id' => $bs->id,
                                ]);
                            } else if (null !== $request->input('note_title')[$n]) {

                                $ndArray = (null !== $request->input('note_date')[$n] ? explode('/',$request->input('note_date')[$n]) : null);
                                $noteDate = (null !== $ndArray ? $ndArray[2] . '-' . $ndArray[1] . '-' . $ndArray[0] : null);

                                BuildingSiteNote::create([
                                    'note_title' => $request->input('note_title')[$n],
                                    'note_body' => $request->input('note_body')[$n],
                                    'note_date' => $noteDate,
                                    'building_site_id' => $bs->id,
                                ]);
                            }
                        }
                    } 

                } catch (\Exception $e) {
                    Log::error('Errore durante la creazione di un cantiere: '. $e->getMessage());
                    abort(500, $e->getMessage());
                }


                // Creating a different try-catch so we can avoid stopping the user BS flow if an error occurs on the emails
                try {
                    // Send an email alert to the users that have been assigned to this building site
                    //$this->sendEmailAlert($bs, $bs->employees()->get());

                    // Send an email alert to the building site manager if it has changed
                    $manager = User::find($request->input('manager_id'));
                    if (null !== $manager) {
                        // Mail::to($manager->email)
                        //     ->send(new BuildingSiteAssignement($bs, true));
                    }
                } catch (\Exception $e) {
                    Log::error('Errore durante l\'invio email nella modifica di un cantiere: '. $e->getMessage());
                }
            }

            return redirect()->route('building-sites.edit', $bs->id);
        }

        /**
         * Display the specified resource.
         *
         * @param  \App\BuildingSite $buildingSite
         * @return \Illuminate\Http\Response
         */
        public function show(BuildingSite $buildingSite)
        {
            $bsMedia = $buildingSite->media('image')->get();
            $materials = json_decode($buildingSite->materials);
            $mediaToday = $buildingSite->mediaToday(auth()->user()->name . ' ' . auth()->user()->surname,$buildingSite->id);
            $mediaTodayCount = $buildingSite->mediaTodayCount(auth()->user()->name . ' ' . auth()->user()->surname,$buildingSite->id);

            return view('backend.building-sites.view-building-site-employee', compact('buildingSite', 'materials', 'bsMedia', 'mediaToday','mediaTodayCount'));
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param BuildingSite $buildingSite
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function edit(BuildingSite $buildingSite)
        {
            // Validate if the user is authorised to perform this action
            if ($this->authorize('update', $buildingSite)) {

                $materials = json_decode($buildingSite->materials);
                $bsMedia = $buildingSite->media('image')->get();
                $bsFiles = $buildingSite->media('file')->get();

                $assignedEmployees = $buildingSite->employees()
                    ->get()
                    ->pluck('id')
                    ->toArray();

                return view('backend.building-sites.add-building-site',
                    compact('buildingSite', 'materials', 'bsMedia', 'bsFiles', 'assignedEmployees'));
            }
        }

        /**
         * Update the specified resource in storage.
         *
         * @param Request $request
         * @param BuildingSite $buildingSite
         * @return \Illuminate\Http\RedirectResponse
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function update(Request $request, BuildingSite $buildingSite)
        {
            $request->validate([
                'site_name' => 'required',
                'manager_id' => 'required|exists:users,id',
                'customer_id' => 'required|exists:customers,id',
                'machines' => 'nullable|array',
            ]);

            // Validate if the user is authorised to perform this action
            if ($this->authorize('update', $buildingSite)) {

                // Verify the uniqueness of the building site name only if the name has changed
                if ($buildingSite->site_name != $request->input('site_name')) {
//                    $request->validate([
//                        'site_name' => 'unique:building_sites,site_name',
//                    ]);

                    $buildingSite->site_name = $request->input('site_name');
                }


                // Update the building site
                try {
                    // Send an email alert to the building site manager if it has changed
                    if ($buildingSite->manager_id != $request->input('manager_id')) {
                        $manager = User::find($request->input('manager_id'));
                        // Mail::to($manager->email)
                        //     ->send(new BuildingSiteAssignement($buildingSite, true));
                    }

                    // Formatting the quote_date field
                    if (null !== $request->input('quote_date')) {
                        $quoteDateArray = explode('/', $request->input('quote_date'));
                        $quoteDate = $quoteDateArray[2] . '-' . $quoteDateArray[1] . '-' . $quoteDateArray[0];
                    }

                    // Form a json version of the address
                    $address = [
                        'lat' => $request->input('lat'),
                        'lng' => $request->input('lng'),
                        'street_number' => $request->input('street_number'),
                        'route' => $request->input('route'),
                        'locality' => $request->input('locality'),
                        'administrative_area_level_1' => $request->input('administrative_area_level_1'),
                        'country' => $request->input('country'),
                        'postal_code' => $request->input('postal_code'),
                        'autocomplete' => $request->input('address')
                    ];

                    // Form a json version of the materials
                    $materials = [
                        'materials' => $request->input('materials'),
                        'qty' => $request->input('material_qty'),
                        'base' => $request->input('base'),
                    ];

                    // Add new materials to the materials table
                    foreach($request->input('materials') as $material) {
                        if (null !== $material) {
                            $materialName = strtolower($material);
                            $exists = DB::table('materials')
                            ->whereRaw("LOWER(material_name) = '".addslashes($materialName)."'")
                                ->count();

                            if ($exists == 0) {
                                Material::create([
                                    'material_name' => $material
                                ]);
                            }
                        }
                    }

                    $buildingSite->manager_id = $request->input('manager_id');

                    $buildingSite->customer_id = $request->input('customer_id');

//                    $buildingSite->notes = $request->input('notes');

                    $buildingSite->customer_notes = $request->input('customer_notes');

                    $buildingSite->site_type = (null !== $request->input('site_type') ? json_encode($request->input('site_type')) : null);

                    $buildingSite->address = json_encode($address);

                    $buildingSite->materials = json_encode($materials);

                    $buildingSite->quote_number = $request->input('quote_number');

                    $buildingSite->quote_date = (isset($quoteDate) ? $quoteDate : null);

                    $buildingSite->order_number = $request->input('order_number');

                    $buildingSite->updated_by = auth()->user()->name . ' ' . auth()->user()->surname;

                    $buildingSite->save();

                    // Update the selected machines in the bs-machinery middle table
                    $buildingSite->machineries()
                        ->detach();
                    $buildingSite->machineries()
                        ->attach($request->input('machines'), [
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $buildingSite->updated_by,
                        ]);

                    /**
                     * Get the employees currently assigned to this building site, we'll then use the list of employees
                     * generated as third parameter for the sendEmailAlert function, so it will automatically exclude
                     * those employees that have already received the e-mail message from the list.
                     */
                    $employees = $buildingSite->employees()
                        ->get()
                        ->pluck('id')
                        ->toArray();

                    // Update the employees that should have visibility on this building site

                    // !!!!! N.B. this function should be always left AFTER the $employees variable above !!!!!

                    $buildingSite->employees()
                        ->sync($request->input('employee_visibility'));

                    // !!!!! N.B. this function should be always left AFTER the $employees variable above !!!!!


                    // Clear all the previously saved notes
                    BuildingSiteNote::where('building_site_id', '=', $buildingSite->id)
                        ->delete();
           
                    
                    // Update the building site notes
                    if (null !== $request->input('note_body')) {
                        for($n=0; $n<count($request->input('note_body')); $n++) {
                            if (null !== $request->input('note_body')[$n]) {
                                $ndArray = (null !== $request->input('note_date')[$n] ? explode('/',
                                    $request->input('note_date')[$n]) : null);
                                $noteDate = (null !== $ndArray ? $ndArray[2] . '-' . $ndArray[1] . '-' . $ndArray[0] : null);

                                BuildingSiteNote::create([
                                    'note_title' => $request->input('note_title')[$n],
                                    'note_body' => $request->input('note_body')[$n],
                                    'note_date' => $noteDate,
                                    'building_site_id' => $buildingSite->id,
                                ]);
                            } else if (null !== $request->input('note_title')[$n]) {

                                $ndArray = (null !== $request->input('note_date')[$n] ? explode('/',
                                    $request->input('note_date')[$n]) : null);
                                $noteDate = (null !== $ndArray ? $ndArray[2] . '-' . $ndArray[1] . '-' . $ndArray[0] : null);

                                BuildingSiteNote::create([
                                    'note_title' => $request->input('note_title')[$n],
                                    'note_body' => $request->input('note_body')[$n],
                                    'note_date' => $noteDate,
                                    'building_site_id' => $buildingSite->id,
                                ]);
                            }
                        }
                    }


                } catch (\Exception $e) {
                    Log::error('Errore durante la modifica di un cantiere: '. $e->getMessage());
                    abort(500, $e->getMessage());
                }


                // Creating a different try-catch to avoid stopping the user flow during the updating process
                try {
                    // Send an email alert to the users that have been assigned to this building site
                    //$this->sendEmailAlert($buildingSite, $buildingSite->employees()->get(), $employees);
                } catch (\Exception $e) {
                    Log::error('Errore durante l\'invio email nella modifica di un cantiere: '. $e->getMessage());
                }
            }

            return redirect()->route('building-sites.edit', $buildingSite->id);
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param BuildingSite $buildingSite
         * @return mixed
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function destroy(BuildingSite $buildingSite)
        {
            if ($this->authorize('delete', $buildingSite)) {
                // Delete the building site
                try {

                    // Check for reports
                    $reports = Report::where('building_site_id', '=', $buildingSite->id);
                    if ($reports->count() > 0) {
                        return $this->respondBadRequest('Non è possibile eliminare questo cantiere in quanto ha dei rapportini assegnati.');
                    }

                    // Check for customer reports
                    $reports = CustomerReport::where('building_site_id', '=', $buildingSite->id);
                    if ($reports->count() > 0) {
                        return $this->respondBadRequest('Non è possibile eliminare questo cantiere in quanto ha dei rapportini cliente assegnati.');
                    }

                    // We should remove the records in the building_site_machinery table
                    DB::table('building_site_machinery')
                        ->where('building_site_id', '=', $buildingSite->id)
                        ->delete();

                    // We do a soft delete on the building site record
                    $buildingSite->delete();

                    // TODO: What should we do with the data attached to this building site?

                } catch (\Exception $e) {
                    Log::error('Errore durante l\'eliminazione di un cantiere: '. $e->getMessage());
                    abort(500, $e->getMessage());
                }
            }
        }

        /**
         * Function used to set a building site as closed
         *
         * @param BuildingSite $buildingSite
         * @return \Illuminate\Http\RedirectResponse
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function closeBuildingSite(BuildingSite $buildingSite)
        {
            if ($this->authorize('update', $buildingSite)) {

                // Close the building site
                try {
                    $buildingSite->status = 'closed';

                    $buildingSite->closing_date = date('Y-m-d H:i:s');

                    $buildingSite->closed_by = auth()->user()->name . ' ' . auth()->user()->surname;

                    $buildingSite->save();

                } catch (\Exception $e) {
                    Log::error('Errore durante la chiusura di un cantiere: '. $e->getMessage());
                    abort(500, $e->getMessage());
                }
            }

            return redirect()->route('building-sites.index');
        }

        /**
         * @param Request $request
         * @param BuildingSite $buildingSite
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function buildingSiteReports(Request $request, BuildingSite $buildingSite)
        {
            $startDate = $request->get('date_from');
//            $endDate = $request->get('date_to');


            // Display all the stats based on the month selected (if any)
            if (null !== $startDate) {
                $sdArray = explode('-', $startDate);
                $dateFrom = new Carbon(date("{$sdArray[2]}-{$sdArray[1]}-{$sdArray[0]} 00:00:00"));
                $dateTo = new Carbon(date("{$sdArray[2]}-{$sdArray[1]}-t 23:59:59"));
            } else {
                $dateFrom = new Carbon(date("Y-m-01 00:00:00"));
                $dateTo = new Carbon(date("Y-m-t 23:59:59"));
            }


//            if (null !== $endDate) {
//                $edArray = explode('-', $endDate);
//                $dateTo = new Carbon(date("{$edArray[2]}-{$edArray[1]}-{$edArray[0]} 23:59:59"));
//            } else {
//                $dateTo = new Carbon(date("Y-m-t 23:59:59"));
//            }

            return view('backend.reports.bs-charts', compact('buildingSite', 'dateFrom', 'dateTo'));
        }


        /**
         * Shows the window used to upload media files for a specific building site, by geotagging the user and
         * storing all his data next to the image record
         *
         * @param BuildingSite $buildingSite
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function uploadMediaFiles(BuildingSite $buildingSite)
        {
            return view('backend.media.add-building-site-images', compact('buildingSite'));
        }


        /**
         * Shows the window used to upload media files for a specific building site, by geotagging the user and
         * storing all his data next to the image record
         *
         * @param BuildingSite $buildingSite
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function showMediaFiles(BuildingSite $buildingSite)
        {
            // Fetch media with pagination
            $media = $buildingSite->media(
                'image',
                false,
                ['type' => 'created_at', 'direction' => 'desc'],
                true
            )->paginate(16);
            
            // Return the view with the paginated media
            return view('backend.media.show-building-site-images', compact('buildingSite', 'media'));
        }



        /**
         * Function used to upload documents
         *
         * @param $uploadedFile
         * @param int $buildingSiteId
         */
        /*private function documentUpload($uploadedFile, int $buildingSiteId)
        {
            // TODO: cosa fare se l'utente carica un file con lo stesso nome di un file già presente nella cartella?
            if (null !== $uploadedFile) {

                // Compose a directory name based on the building site
                $directory = "documents/building-site-{$buildingSiteId}";

                // Check if the directory exists, otherwise create it
                if (!File::exists(storage_path($directory))) {
                    File::makeDirectory(storage_path($directory), 0775, true);
                }

                try {
                    // Process the filename
                    $documentExt = strtolower($uploadedFile->getClientOriginalExtension());
                    $documentName = sluggifyFileName($uploadedFile->getClientOriginalName(), $documentExt);

                    // Store the attachment
                    $attachment = $uploadedFile->storeAs(
                        $directory,
                        $documentName. '.' . $documentExt
                    );

                    // Create a row in the media table to link the building site and the document
                    $media = Media::create([
                        'media_name' => $documentName,
                        'extension' => $documentExt,
                        'directory' => "building-site-{$buildingSiteId}",
                        'media_type' => 'file',
                        'mediable_id' => $buildingSiteId,
                        'mediable_type' => 'App\BuildingSite',
                        'created_by' => auth()->user()->name . ' ' . auth()->user()->surname
                    ]);

                } catch (\Exception $e) {
                    Log::error('Errore durante il caricamento di un allegato cantiere: '. $e->getMessage());
                    abort(500, 'Si &egrave; verificato un errore durante il caricamento dell\'allegato.');
                }
            }
        }*/

        /**
         * @param BuildingSite $buildingSite
         * @param Collection $employees
         * @param array|null $excludeEmployees
         */
        private function sendEmailAlert(BuildingSite $buildingSite, Collection $employees, array $excludeEmployees = [])
        {
            foreach($employees as $employee) {
                if (!in_array($employee->id, $excludeEmployees)) {
                    Mail::to($employee->email)
                        ->send(new BuildingSiteAssignement($buildingSite));
                }
            }
        }
    }
