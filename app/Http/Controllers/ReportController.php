<?php

    namespace App\Http\Controllers;

    use App\BuildingSite;
    use App\Report;
    use App\ReportRow;
    use App\User;
    use Barryvdh\DomPDF\Facade as PDF;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;
    use App\Exports\ReportExcelExport;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\URL;

    class ReportController extends Controller
    {

        protected $validationMessages = [
            'building_site_id.required' => 'Devi specificare l\'id di un cantiere.',
            'truck_no.required' => 'Il numero del camion è obbligatorio.',
            'truck_driver_name.required' => 'Il nome del guidatore è obbligatorio.',
            'travel_time.numeric' => 'Il campo relativo alle ore di viaggio deve essere di tipo numerico.',
            'employees.required' => 'Devi specificare il nome degli operai in cantiere.',
            'job_other_text.required_with' => 'Devi aggiungere delle specifiche per il tipo di lavorazione "Altro".',
            'travel_time.required' => 'Devi aggiungere il tempo del viaggio',
            'meals_no.required' => 'Devi aggiungere i pasti',
            'total_break_time.required' => 'Devi aggiungere le ore di pausa',
            'break_from_to.required' => "Devi aggiungere l'intervallo della pausa",
        ];

        /**
         * Display a listing of the resource.
         *
         * @param Request $request
         * @param string|null $startDate
         * @param string|null $endDate
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function index(Request $request)
        {

            $startDate = $request->get('date_from');
            $endDate = $request->get('date_to');
            $employeeIdFilter = $request->get('eid');
            $buildingSiteIdFilter = $request->get('building_site_id');
            
            ($buildingSiteIdFilter != '' AND $startDate === null AND $endDate === null ) ? $nodate = true : $nodate = false;


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


            // Lista dei dipendenti
            $user = new User();
            $employees = $user->getEmployeeList(false, true);

            // Get a count of the monthly report
            if ($nodate) {
                $reports = Report::where('report_type', 'daily')
                    ->whereHas('employee')
                    ->whereHas('buildingSite')
                    ->where(function($q) use ($buildingSiteIdFilter, $employeeIdFilter) {
                        if (null !== $buildingSiteIdFilter) {
                            $q->where('building_site_id', '=', $buildingSiteIdFilter);
                        }

                        if (null !== $employeeIdFilter) {
                            $q->where('user_id', '=', $employeeIdFilter);
                        }
                        
                        if(!auth()->user()->isAdmin()) {
                            $q->where('user_id', '=', auth()->user()->id);
                            $q->where('created_at', '>', Carbon::now()->subDays(10));
                        }
                    })
                    ->orderBy('id', 'desc')
                    ->get();                
            } else {
                $reports = Report::where('report_type', 'daily')
                    ->whereBetween('created_at', [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')])
                    ->whereHas('employee')
                    ->whereHas('buildingSite')
                    ->where(function($q) use ($buildingSiteIdFilter, $employeeIdFilter) {
                        if (null !== $buildingSiteIdFilter) {
                            $q->where('building_site_id', '=', $buildingSiteIdFilter);
                        }

                        if (null !== $employeeIdFilter) {
                            $q->where('user_id', '=', $employeeIdFilter);
                        }
                        
                        if(!auth()->user()->isAdmin()) {
                            $q->where('user_id', '=', auth()->user()->id);
                            $q->where('created_at', '>', Carbon::now()->subDays(10));
                        }
                    })
                    ->orderBy('id', 'desc')
                    ->get();                
            }


            // Lista dei cantieri
            $buildingSites = BuildingSite::where(function($q) use ($reports) {
                $q->whereIn('id', $reports->pluck('building_site_id')->toArray());
            })
                ->orderBy('site_name', 'asc')
                ->get();

            return view('backend.reports.report-list',
                compact('reports', 'dateFrom', 'dateTo', 'buildingSiteIdFilter', 'employees', 'employeeIdFilter', 'buildingSites'));
        }
        
        function notCompiledReport(Request $request) {
            
            $startDate = $request->get('date_from');
            $endDate = $request->get('date_to');
            $ignoreUser = $request->get('ignore_user');
            $ignoreDate = $request->get('ignore_date');

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
            
            if (null !== $ignoreUser AND null !== $ignoreDate) {
                DB::table('reports_ignore')->insertOrIgnore([
                    'user_id' => $ignoreUser,
                    'date' => $ignoreDate
                ]);
            }
            
            // Lista dei dipendenti
            $user = new User();
            //$employees = $user->getEmployeeList(false, true);
            $employees = $user->getRoleEmployeeList(false, true);
            
            return view('backend.reports.report-not-compiled-list',
                compact('dateFrom', 'dateTo', 'employees'));            
        }

        /**
         * Show the form for creating a new resource.
         *
         * @param BuildingSite $buildingSite
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function create(BuildingSite $buildingSite)
        {
            if ($this->authorize('create', Report::class)) {


                $status = checkOpenedBuildingSite($buildingSite->id); //controllo lo status del cantiere
                $utente = Auth::id();
                $ruolo = checkUserRole($utente); //controllo i permessi dell'utente

                if($status == 'closed' AND $ruolo == 'employee'){
                    //se il cantiere è chiuso e non hai permessi non puoi compilare il rapportino
                    //return redirect("/building-sites/{$buildingSite->id}");
                    echo"<script>alert('Non puoi compilare questo rapportino, il cantiere è chiuso');
                        window.location.replace('/building-sites/".$buildingSite->id."');
                    </script>";
                    return redirect("/building-sites/{$buildingSite->id}");
                    //return redirect("/building-sites/{$buildingSite->id}")->with('popup_message', 'Non puoi compilare il rapportino per questo cantiere');
                    
                }
                
                $assignedEmployees = $buildingSite->employees()
                    ->get()
                    ->pluck('id')
                    ->toArray();
                
                return view('backend.reports.daily-employee-report', compact('buildingSite','assignedEmployees'));
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
            if ($this->authorize('create', Report::class)) {

                // Calcolare il totale delle ore lavoro facendo la differenza tra l'ora d'inizio e l'ora di fine
                $timeStart = new Carbon($request->input('date_time_start'));
                $timeEnd = new Carbon($request->input('date_time_end'));
                $totWorkingSeconds = ($timeEnd->diffInSeconds($timeStart) - (null !== $request->input('total_break_time') ? ($request->input('total_break_time') * 3600) : 0));
                $totalHours = gmdate('H', $totWorkingSeconds);
                $totalMinutes = gmdate('i', $totWorkingSeconds);
                $totWorkingHours = $totalHours + ($totalMinutes > 0 ? $totalMinutes / 60 : 0);

                $created_at = Carbon::now();
                
                if ($request->input('shift_type') === 'notturno') {
                    $timeStart->subDay(); // sottrai un giorno dalla data di inizio
                }
                
                $validator = Validator::make($request->all(), [
                    'building_site_id' => 'required|exists:building_sites,id',
                    'truck_no' => 'required',
                    'truck_driver_name' => 'required',
                    'travel_time' => 'nullable',
                    'employees' => 'required|array',
                    'job_coperture' => 'nullable',
                    'job_stuccature' => 'nullable',
                    'job_carteggiatura' => 'nullable',
                    'job_lavaggio' => 'nullable',
                    'job_sabbiatura' => 'nullable',
                    'job_verniciatura' => 'nullable',
                    'job_discarica' => 'nullable',
                    'job_lavaggio_camion' => 'nullable',
                    'job_ritiro_materiale' => 'nullable',
                    'job_ordine_dentro_capannone' => 'nullable',
                    'job_ordine_fuori_capannone' => 'nullable',
                    'job_pulizia' => 'nullable',
                    'job_other' => 'nullable',
                    'job_coperture_details' => 'required_with:job_coperture',
                    'job_stuccature_details' => 'required_with:job_stuccature',
                    'job_carteggiatura_details' => 'required_with:job_carteggiatura',
                    'job_lavaggio_details' => 'required_with:job_lavaggio',
                    'job_sabbiatura_details' => 'required_with:job_sabbiatura',
                    'job_verniciatura_details' => 'required_with:job_verniciatura',
                    'job_intonaco_details' => 'required_with:job_intonaco',
                    'job_other_text' => 'required_with:job_other',
                
                ], $this->validationMessages);
                
                $jobFields = array_filter($request->only([
                    'job_coperture',
                    'job_stuccature',
                    'job_carteggiatura',
                    'job_lavaggio',
                    'job_sabbiatura',
                    'job_verniciatura',
                    'job_discarica',
                    'job_lavaggio_camion',
                    'job_ritiro_materiale',
                    'job_ordine_dentro_capannone',
                    'job_ordine_fuori_capannone',
                    'job_pulizia',
                    'job_other'
                ]), function ($value) {
                    return $value !== null && $value !== false && $value !== '0';
                });
                
                if (empty($jobFields)) {
                    $validator->errors()->add('job_selection', 'Seleziona almeno un tipo di lavoro.');
                }
                
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withInput($request->input())
                        ->withErrors($validator->errors());
                }

                $jobDetails = [
                    'job_coperture' => $request->input('job_coperture_details'),
                    'job_stuccature' => $request->input('job_stuccature_details'),
                    'job_carteggiatura' => $request->input('job_carteggiatura_details'),
                    'job_lavaggio' => $request->input('job_lavaggio_details'),
                    'job_sabbiatura' => $request->input('job_sabbiatura_details'),
                    'job_verniciatura' => $request->input('job_verniciatura_details'),
                    'job_intonaco' => $request->input('job_intonaco_details'),

                    'job_discarica' => $request->input('job_discarica_details'),
                    'job_lavaggio_camion' => $request->input('job_lavaggio_camion_details'),
                    'job_ritiro_materiale' => $request->input('job_ritiro_materiale_details'),
                    'job_ordine_dentro_capannone' => $request->input('job_ordine_dentro_capannone_details'),
                    'job_ordine_fuori_capannone' => $request->input('job_ordine_fuori_capannone_details'),
                    'job_pulizia' => $request->input('job_pulizia_details'),

                    'job_other' => $request->input('job_other_details'),
                    'job_other_text' => $request->input('job_other_text'),  // dettagli lavorazione per "altro"
                ];
                $equipment = [];
                $materials = [];
                $totPetrolUsed = 0;

                // Loop all the input data and create the various json arrays
                foreach($request->input() as $key => $val) {
                    if (false !== strpos($key, 'equipment_') and null !== $val) {
                        $equipment[$key] = $val;
                    } else if (false !== strpos($key, 'materials_') and null !== $val) {
                        $materials[$key] = $val;

                        // Sum up the fields used for storing the petrol usage
                        if (in_array($key, ['materials_gasolio_camion', 'materials_gasolio_compressore', 'materials_gasolio_altro']) and (int)$val > 0) {
                            $totPetrolUsed += $val;
                        }
                    }
                }

                $employees = (null !== $request->input('employees') ? json_encode($request->input('employees')) : null);

                // TODO: Controllare calcoli per travel time e totale ore pausa


                // Calcolare il totale delle ore lavoro facendo la differenza tra l'ora d'inizio e l'ora di fine
                //timeStart = new Carbon($request->input('date_time_start'));
                //timeEnd = new Carbon($request->input('date_time_end'));
                $totWorkingSeconds = ($timeEnd->diffInSeconds($timeStart) - (null !== $request->input('total_break_time') ? ($request->input('total_break_time')*3600) : 0));
                $totalHours = gmdate('H', $totWorkingSeconds);
                $totalMinutes = gmdate('i', $totWorkingSeconds);
                $totWorkingHours = $totalHours + ($totalMinutes > 0 ? $totalMinutes / 60 : 0);

                /**
                 * TODO: Controllare che il totale delle ore lavoro corrisponda al totale sommato delle ore di ogni
                 * TODO: singola lavorazione contenuta nell'array job_details[]
                 */

                try {
                    $report = Report::create([
                        'truck_no' => $request->input('truck_no'),
                        'truck_driver_name' => $request->input('truck_driver_name'),
                        'meals_no' => $request->input('meals_no'),
                        'time_start' => $timeStart->toDateTimeString(),
                        'time_end' => $request->input('date_time_end'),
                        'total_working_hours' => $totWorkingHours,
                        'total_break_time' => $request->input('total_break_time'),
                        'break_from_to' => $request->input('break_from_to'),
                        'travel_time' => $request->input('travel_time'),
                        'employees' => $employees,
                        'job_details' => (count($jobDetails) > 0 ? json_encode($jobDetails) : null),
                        'equipment' => (count($equipment) > 0 ? json_encode($equipment) : null),
                        'work_description' => $request->input('work_description'),
                        'extra_work_description' => $request->input('extra_work_description'),
                        'time_lost' => $request->input('time_lost'),
                        'materials' => (count($materials) > 0 ? json_encode($materials) : null),
                        'extra_expenses' => $request->input('extra_expenses'),
                        'tot_petrol_used' => $totPetrolUsed,
                        'location_lat' => $request->input('location_lat'),
                        'location_lng' => $request->input('location_lng'),
                        'report_type' => 'daily',
                        'report_view' => $request->input('report_view'),
                        'user_id' => auth()->user()->id,
                        'building_site_id' => $request->input('building_site_id'),
                        'shift_type' => $request->input('shift_type'),
                        'created_at' => $created_at // imposta il created_at modificato
                    ]);


                    // Add report rows
                    if (null !== $request->input('mq_lavorati_tot')) {
                        createReportRows(
                            $report->id,
                            $request->input('mq_lavorati_tot'),
                            $request->input('mq_lavorati_x'),
                            $request->input('mq_lavorati_y'),
                            $request->input('mq_lavorati_z'),
                            $request->input('qty'),
                            $request->input('work_type'),
                            $request->input('struttura'),
                            $request->input('materiale')
                        );
                    }

                    // Set the toast notification
                    \request()->session()->flash('toast-class', 'success');
                    \request()->session()->flash('toast', 'Rapportino creato correttamente.');

                } catch (\Exception $e) {
                    $strRandId = $this->generateErrorIdentifier();
                    Log::error($strRandId . ' - Errore durante la creazione di un report: '. $e->getMessage());

                    // Set the toast notification
                    \request()->session()->flash('toast-class', 'error');
                    \request()->session()->flash('toast', 'Errore durante la creazione del rapportino. Rif errore: ' . $strRandId);
                }

                return redirect()->route('building-sites.index');
            }
        }

        /**
         * Display the specified resource.
         *
         * @param  \App\Report $report
         * @return \Illuminate\Http\Response
         */
        public function show(Report $report)
        {
            $timeStart = new Carbon($report->time_start);
            $timeEnd = new Carbon($report->time_end);

            if ('internal' == $report->report_view) {
                $viewName = 'view-internal-report';
            } else {
                $viewName = 'view-daily-employee-report';
            }

            return view("backend.reports.$viewName", compact('report', 'timeStart', 'timeEnd'));
        }


        /**
         * Displays the daily employee report edit page
         *
         * @param Report $report
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function edit(Report $report)
        {
            if ($this->authorize('update', $report)) {

                // Get the building site
                $buildingSite = $report->buildingSite()->first();

                // Count for strutture sabbiate
                $sabb = $report->rows('S');
                $totSabbiato = 0;
                foreach($sabb->get() as $s) {
                    $totSabbiato += ($s->qty * $s->mq_lavorati_tot);
                }

                // Count for strutture verniciate
                $vern = $report->rows('V');
                $totVerniciato = 0;
                foreach($vern->get() as $v) {
                    $totVerniciato += ($v->qty * $v->mq_lavorati_tot);
                }

                // Count for strutture verniciate
                $other = $report->rows('');
                $totAltro = 0;
                foreach($other->get() as $o) {
                    $totAltro += ($o->qty * $o->mq_lavorati_tot);
                }

                $jobDetails = (array)json_decode($report->job_details);
                $jobEquipment = (array)json_decode($report->equipment);
                $jobMaterials = (array)json_decode($report->materials);


                if ('internal' == $report->report_view) {
                    $viewName = 'create-internal-report';
                } else {
                    $viewName = 'daily-employee-report';
                }

                return view("backend.reports.$viewName",
                    compact('buildingSite', 'report', 'totSabbiato', 'totVerniciato', 'totAltro', 'jobDetails', 'jobEquipment', 'jobMaterials'));
            }
        }


        /**
         * Function used to update the daily employee report
         *
         * @param Request $request
         * @param Report $report
         * @return $this|\Illuminate\Http\RedirectResponse
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function update(Request $request, Report $report)
        {
            if ($this->authorize('update', $report)) {

                $validator = Validator::make($request->all(), [
                    'building_site_id' => 'required|exists:building_sites,id',
                    'truck_no' => 'required',
                    'truck_driver_name' => 'required',
                    'travel_time' => 'nullable|numeric',
                    'employees' => 'required|array',
                
                    'job_coperture' => 'nullable',
                    'job_stuccature' => 'nullable',
                    'job_carteggiatura' => 'nullable',
                    'job_lavaggio' => 'nullable',
                    'job_sabbiatura' => 'nullable',
                    'job_verniciatura' => 'nullable',
                    'job_discarica' => 'nullable',
                    'job_lavaggio_camion' => 'nullable',
                    'job_ritiro_materiale' => 'nullable',
                    'job_ordine_dentro_capannone' => 'nullable',
                    'job_ordine_fuori_capannone' => 'nullable',
                    'job_pulizia' => 'nullable',
                    'job_other' => 'nullable',
                
                    'job_coperture_details' => 'required_with:job_coperture',
                    'job_stuccature_details' => 'required_with:job_stuccature',
                    'job_carteggiatura_details' => 'required_with:job_carteggiatura',
                    'job_lavaggio_details' => 'required_with:job_lavaggio',
                    'job_sabbiatura_details' => 'required_with:job_sabbiatura',
                    'job_verniciatura_details' => 'required_with:job_verniciatura',
                    'job_intonaco_details' => 'required_with:job_intonaco',
                    'job_other_text' => 'required_with:job_other',
                
                ], $this->validationMessages);
                
                $jobFields = array_filter($request->only([
                    'job_coperture',
                    'job_stuccature',
                    'job_carteggiatura',
                    'job_lavaggio',
                    'job_sabbiatura',
                    'job_verniciatura',
                    'job_discarica',
                    'job_lavaggio_camion',
                    'job_ritiro_materiale',
                    'job_ordine_dentro_capannone',
                    'job_ordine_fuori_capannone',
                    'job_pulizia',
                    'job_other'
                ]), function ($value) {
                    return $value !== null && $value !== false && $value !== '0';
                });
                
                if (empty($jobFields)) {
                    $validator->errors()->add('job_selection', 'Seleziona almeno un tipo di lavoro.');
                }
                
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withInput($request->input())
                        ->withErrors($validator->errors());
                }                

                $jobDetails = [
                    'job_coperture' => $request->input('job_coperture_details'),
                    'job_stuccature' => $request->input('job_stuccature_details'),
                    'job_carteggiatura' => $request->input('job_carteggiatura_details'),
                    'job_lavaggio' => $request->input('job_lavaggio_details'),
                    'job_sabbiatura' => $request->input('job_sabbiatura_details'),
                    'job_verniciatura' => $request->input('job_verniciatura_details'),
                    'job_intonaco' => $request->input('job_intonaco_details'),

                    'job_discarica' => $request->input('job_discarica_details'),
                    'job_lavaggio_camion' => $request->input('job_lavaggio_camion_details'),
                    'job_ritiro_materiale' => $request->input('job_ritiro_materiale_details'),
                    'job_ordine_dentro_capannone' => $request->input('job_ordine_dentro_capannone_details'),
                    'job_ordine_fuori_capannone' => $request->input('job_ordine_fuori_capannone_details'),
                    'job_pulizia' => $request->input('job_pulizia_details'),
                    'job_other' => $request->input('job_other_details'),
                    'job_other_text' => $request->input('job_other_text'),  // dettagli lavorazione per "altro"
                ];
                $equipment = [];
                $materials = [];
                $totPetrolUsed = 0;

                // Loop all the input data and create the various json arrays
                foreach($request->input() as $key => $val) {
                    if (false !== strpos($key, 'equipment_') and null !== $val) {
                        $equipment[$key] = $val;
                    } else if (false !== strpos($key, 'materials_') and null !== $val) {
                        $materials[$key] = $val;

                        // Sum up the fields used for storing the petrol usage
                        if (in_array($key, ['materials_gasolio_camion', 'materials_gasolio_compressore', 'materials_gasolio_altro']) and (int)$val > 0) {
                            $totPetrolUsed += $val;
                        }
                    }
                }

                $employees = (null !== $request->input('employees') ? json_encode($request->input('employees')) : null);

                // Calcolare il totale delle ore lavoro facendo la differenza tra l'ora d'inizio e l'ora di fine
                $timeStart = new Carbon($request->input('date_time_start'));
                $timeEnd = new Carbon($request->input('date_time_end'));

                // Preserva la data originale del report per ora e minuti
                if ($report->time_start) {
                    $originalStart = new Carbon($report->time_start);
                    $timeStart->setDate($originalStart->year, $originalStart->month, $originalStart->day);
                }

                if ($report->time_end) {
                    $originalEnd = new Carbon($report->time_end);
                    $timeEnd->setDate($originalEnd->year, $originalEnd->month, $originalEnd->day);
                }

                // Gestione del turno notturno
                //if ($request->input('shift_type') === 'notturno') {
                //    $timeStart->subDay(); // sottrai un giorno dalla data di inizio
                //}

                $totWorkingSeconds = ($timeEnd->diffInSeconds($timeStart) - (null !== $request->input('total_break_time') ? ($request->input('total_break_time')*3600) : 0));
                $totalHours = gmdate('H', $totWorkingSeconds);
                $totalMinutes = gmdate('i', $totWorkingSeconds);
                $totWorkingHours = $totalHours + ($totalMinutes > 0 ? $totalMinutes / 60 : 0);

                try {
                    $report->truck_no = $request->input('truck_no');

                    $report->truck_driver_name = $request->input('truck_driver_name');

                    $report->meals_no = $request->input('meals_no');

                    $report->time_start = $timeStart->toDateTimeString();

                    $report->time_end = $timeEnd->toDateTimeString();

                    $report->total_working_hours = $totWorkingHours;

                    $report->total_break_time = $request->input('total_break_time');

                    $report->break_from_to = $request->input('break_from_to');

                    $report->travel_time = $request->input('travel_time');

                    $report->employees = $employees;

                    $report->job_details = (count($jobDetails) > 0 ? json_encode($jobDetails) : null);

                    $report->equipment = (count($equipment) > 0 ? json_encode($equipment) : null);

                    $report->work_description = $request->input('work_description');

                    $report->extra_work_description = $request->input('extra_work_description');

                    $report->time_lost = $request->input('time_lost');

                    $report->materials = (count($materials) > 0 ? json_encode($materials) : null);

                    $report->extra_expenses = $request->input('extra_expenses');

                    $report->tot_petrol_used = $totPetrolUsed;

                    //$report->location_lat = $request->input('location_lat');

                    //$report->location_lng = $request->input('location_lng');

                    $report->report_type = 'daily';

                    $report->updated_by = auth()->user()->name . ' ' . auth()->user()->surname;

                    $report->shift_type = $request->input('shift_type');

                    $report->save();

                    // Remove previously saved report rows and add any new one
                    /**
                     * N.B. this removal of entries should NOT be done for incomplete reports, otherwise all the
                     * associated report data will be lost too
                     */

                    if ('Y' !== $request->input('incomplete_report')) {
                        $report->rows()->delete();

                        // Add report rows
                        if (null !== $request->input('mq_lavorati_tot')) {
                            createReportRows(
                                $report->id,
                                $request->input('mq_lavorati_tot'),
                                $request->input('mq_lavorati_x'),
                                $request->input('mq_lavorati_y'),
                                $request->input('mq_lavorati_z'),
                                $request->input('qty'),
                                $request->input('work_type'),
                                $request->input('struttura'),
                                $request->input('materiale')
                            );
                        }
                    }

                    // Set the toast notification
                    \request()->session()->flash('toast-class', 'success');
                    \request()->session()->flash('toast', 'Rapportino aggiornato correttamente.');

                } catch (\Exception $e) {
                    $strRandId = $this->generateErrorIdentifier();
                    Log::error($strRandId . ' - Errore durante l\'aggiornamento del report: '. $e->getMessage());

                    // Set the toast notification
                    \request()->session()->flash('toast-class', 'error');
                    \request()->session()->flash('toast', 'Errore durante l\'aggiornamento del rapportino. Rif errore: ' . $strRandId);
                }

                return redirect()->route('building-sites.index');
            }
        }

        
        public function forceclose(Request $request, Report $report)
        {
            if ($this->authorize('update', $report)) {
                $report->report_type = 'daily';
                $report->save();
            }
            
            return redirect()->route('building-sites.index');
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param Report $report
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function destroy(Report $report)
        {
            if ($this->authorize('delete', $report)) {

                try {

                    $ts = Carbon::now()->toDateTimeString();
                    $data = array('deleted_at' => $ts, 'updated_by' => auth()->user()->name . ' ' . auth()->user()->surname);

                    $report->update($data);

                } catch (\Exception $e) {
                    $strRandId = $this->generateErrorIdentifier();
                    Log::error($strRandId . ' - Errore durante l\'eliminazione di un rapportino giornaliero: '. $e->getMessage());

                    // Set the toast notification
                    \request()->session()->flash('toast-class', 'error');
                    \request()->session()->flash('toast', 'Errore durante l\'eliminazione del rapportino. Rif errore: ' . $strRandId);
                }
            }
        }


        /**
         * Function used to display the create internal reports page
         *
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function createCapannone()
        {
            if ($this->authorize('create', Report::class)) {

                return view('backend.reports.create-internal-report');
            }

        }

        /**
         * @param Report $report
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function downloadDailyPdfReport(Report $report)
        {
            // Create a PDF document for the customer report
            return createPdfReport($report);
        }

        /**
         * @param Report $report
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function downloadAllPdfReport(Request $request)
        {
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

            // Lista dei dipendenti
            $user = new User();
            $employees = $user->getEmployeeList(false, true);


            // Get a count of the monthly report
            $reports = Report::where('report_type', 'daily')
                ->whereBetween('created_at', [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')])
                ->whereHas('employee')
                ->whereHas('buildingSite')
                ->where(function($q) use ($buildingSiteIdFilter, $employeeIdFilter) {
                    if (null !== $buildingSiteIdFilter) {
                        $q->where('building_site_id', '=', $buildingSiteIdFilter);
                    }

                    if (null !== $employeeIdFilter) {
                        $q->where('user_id', '=', $employeeIdFilter);
                    }
                })
                ->orderBy('id', 'desc')
                ->get();

            // Lista dei cantieri
            $buildingSites = BuildingSite::where(function($q) use ($reports) {
                $q->whereIn('id', $reports->pluck('building_site_id')->toArray());
            })
                ->orderBy('site_name', 'asc')
                ->get();

            // Create ALL PDF document for the customer report
            return createAllPdfReport($reports);
        }

        
        /**
         * Function used to export employee working hours in csv format
         *
         * @param Request $request
         * @return \Symfony\Component\HttpFoundation\StreamedResponse
         */
        public function employeeHoursCsvExport(Request $request)
        {
            $request->validate([
                'export_month' => 'required|date_format:Y-m-d',
                'employee_id' => 'required|exists:users,id',
            ]);

            try {
                // Set the date timeframe
                $dateFrom = new Carbon($request->input('export_month'));

                // Get the employee details
                $employee = User::find($request->input('employee_id'));
                $csvFileName = Str::slug($employee->name . '-' . $employee->surname) . '-' . $dateFrom->format('m-Y') . '.csv';

                // Recuperare tutti i rapportini giornalieri per questo dipendente nel mese selezionato
                $reports = Report::where('user_id', '=', $request->input('employee_id'))
                    ->whereBetween('time_start', [$dateFrom->format('Y-m-d 00:00:00'), $dateFrom->format('Y-m-t 23:59:59')])
                    ->orderBy('time_start', 'asc')
                    ->get();

                $totOrdin = 0;
                $totHours = 0;
                $totExtra = 0;
                $totSaturday = 0;
                $totSunday = 0;
                $totMeals = 0;

                $dataexport = array();
                foreach ($reports as $report) {
                    $timeStart = new Carbon($report->time_start);
                    $timeEnd = new Carbon($report->time_end);

                    $row['GIORNO'] = $timeStart->format('d') . ' ' . convertDayName($timeStart->format('l'));
                    $row['ORA INIZIO'] = $timeStart->format('H:i');
                    $row['PAUSA'] = (!empty($report->break_from_to) ? '\''.$report->break_from_to : '');
                    $row['ORA FINE'] = $timeEnd->format('H:i');
                    $row['VIAGGIO'] = $report->travel_time;
                    $row['TRASFERTA'] = $report->buildingSite->site_name;
                    $row['TOTALE'] = $report->total_working_hours;

                    $totHours += (! empty($report->total_working_hours) ? $report->total_working_hours : 0);

                    // Check if the working day falls on a saturday or sunday
                    if ($timeStart->format('N') == 6) {
                        $row['ORDIN'] = '';
                        $row['STRAO'] = '';
                        $row['SABATO'] = $report->total_working_hours;
                        $row['DOMENICA'] = '';

                        $totSaturday += $row['SABATO'];

                    } else if ($timeStart->format('N') == 7) {
                        $row['ORDIN'] = '';
                        $row['STRAO'] = '';
                        $row['SABATO'] = '';
                        $row['DOMENICA'] = $report->total_working_hours;

                        $totSunday += $row['DOMENICA'];

                    } else {
                        $row['ORDIN'] = ($report->total_working_hours >= 8 ? 8 : '-' . (8 - $report->total_working_hours));
                        $row['STRAO'] = ($report->total_working_hours > 8 ? $report->total_working_hours - 8 : '');
                        $row['SABATO'] = '';
                        $row['DOMENICA'] = '';

                        $totOrdin += $row['ORDIN'];
                        $totExtra += ((int)$row['STRAO'] > 0 ? $row['STRAO'] : 0);
                    }


                    $row['PASTI'] = $report->meals_no;
                    $row['PASTI PAG'] = (!empty($report->meals_no) ? 10 : '');

                    $totMeals += (!empty($report->meals_no) ? 10 : 0);

                    array_push($dataexport, $row);
                }
                array_push($dataexport, array('TOTALI',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $totHours,
                    $totOrdin,
                    $totExtra,
                    $totSaturday,
                    $totSunday,
                    '',
                    $totMeals));


                return Excel::download(new ReportExcelExport($dataexport), Str::slug($employee->name . '-' . $employee->surname) . '-' . $dateFrom->format('m-Y') . '.xlsx');

            } catch (\Exception $e) {
                Log::error("Errore durante l'esportazione csv utente:{$request->input('employee_id')}, data: {$request->input('export_month')} : ". $e->getMessage());
                abort(500, $e->getMessage());
            }
        }
    }
