<?php

    namespace App\Http\Controllers;

    use App\BuildingSite;
    use App\CustomerReport;
    use App\CustomerReportRows;
    use App\Report;
    use App\ReportRow;    
    use App\User;
    use Barryvdh\DomPDF\Facade as PDF;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;
    use Intervention\Image\Facades\Image;

    class CustomerReportController extends Controller
    {
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
            $buildingSiteIdFilter = $request->get('building_site_id');
            $employeeIdFilter = $request->get('eid');

            // Lista dei dipendenti
            $user = new User();
            $employees = $user->getEmployeeList(false, true);

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


            // Get a count of the monthly report
            $reports = CustomerReport::whereBetween('created_at',
                [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')])
                ->whereHas('employee')
                ->whereHas('buildingSite')
                ->where(function ($q) use ($buildingSiteIdFilter, $employeeIdFilter) {
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

            $startDate = $dateFrom->format('d-m-Y');
            $endDate = $dateTo->format('d-m-Y');

            return view('backend.reports.customer-report-list',
                compact('reports', 'dateFrom', 'dateTo', 'buildingSiteIdFilter', 'employeeIdFilter', 'employees', 'buildingSites','startDate','endDate'));
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
                $customerReport = new CustomerReport();

                // Get all the daily reports that have been created up until today
                $reportsIds = Report::where('report_type', '=', 'daily')
                    ->where('created_at', '<=', date('Y-m-d H:i:s'))
                    ->where('building_site_id', '=', $buildingSite->id)
                    ->whereNull('signed_off_by_report_id')
                    ->get()
                    ->pluck('id')
                    ->toArray();

                // Count for strutture sabbiate
                $sabb = $customerReport->customerReportWorks($reportsIds, 'S');
                $totSabbiato = 0;
                foreach($sabb as $s) {
                    $totSabbiato += ($s->qty * $s->mq_lavorati_tot);
                }

                // Count for strutture verniciate
                $vern = $customerReport->customerReportWorks($reportsIds, 'V');
                $totVerniciato = 0;
                foreach($vern as $v) {
                    $totVerniciato += ($v->qty * $v->mq_lavorati_tot);
                }

                // Count for strutture lavaggio
                $lavaggio = $customerReport->customerReportWorks($reportsIds, 'L');
                $totLavato = 0;
                foreach($lavaggio as $l) {
                    $totLavato += ($l->qty * $l->mq_lavorati_tot);
                }

                // Count for strutture soffiatura
                $soffiatura = $customerReport->customerReportWorks($reportsIds, 'SOFF');
                $totSoffiato = 0;
                foreach($soffiatura as $sf) {
                    $totSoffiato += ($sf->qty * $sf->mq_lavorati_tot);
                }

                // Count for strutture intonaco
                $intonaco = $customerReport->customerReportWorks($reportsIds, 'I');
                $totIntonacato = 0;
                foreach($intonaco as $i) {
                    $totIntonacato += ($i->qty * $i->mq_lavorati_tot);
                }

                // Count for strutture verniciate
                $other = $customerReport->customerReportWorks($reportsIds);
                $totAltro = 0;
                foreach($other as $o) {
                    $totAltro += ($o->qty * $o->mq_lavorati_tot);
                }

                return view(
                    'backend.reports.foglio-fine-cantiere',
                    compact('buildingSite', 'reportsIds', 'totSabbiato', 'totVerniciato', 'totLavato', 'totSoffiato', 'totIntonacato', 'totAltro')
                );
            }
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            $request->validate([
                'company_name' => 'required',
                'company_address' => 'required',
                'company_city' => 'required',
                'building_site_id' => 'required|exists:building_sites,id',
                'png_signature' => 'required',
                'billing_to' => 'required|in:cliente,azienda terza',
                'job_type' => 'required|in:a corpo,a consuntivo,ad euro/mq',
                'work_description' => 'required',
                'employee_name' => 'required',
            ]);


            try {
                // Salva firma del cliente
                $signature = Image::make($request->input('png_signature'));

                // Compose a directory name based on the building site
                $directory = "media/signatures/building-site-{$request->input('building_site_id')}";

                // Check if the directory exists, otherwise create it
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0775, true);
                }

                // Save the signature image
                $signDate = date('YmdHis');
                $signatureName = "cst-signature-{$signDate}.png";
                $signature->save("$directory/{$signatureName}", 90);

                $report = CustomerReport::create([
                    'company_name' => $request->input('company_name'),
                    'company_address' => $request->input('company_address'),
                    'company_city' => $request->input('company_city'),
                    'billing_to' => $request->input('billing_to'),
                    'billing_to_company' => $request->input('billing_to_company'),
                    'job_type' => $request->input('job_type'),
                    'work_description' => $request->input('work_description'),
                    'signature_name' => $request->input('signature_name'),
                    'signature_company_name' => $request->input('signature_company_name'),
                    'employee_name' => $request->input('employee_name'),
                    'additional_notes' => $request->input('additional_notes'),
                    'building_site_id' => $request->input('building_site_id'),
                    'customer_signature' => $directory.'/'.$signatureName,
                    'customer_pdf' => "{$directory}/customer-report-{$signDate}.pdf",
                    'lat' => $request->input('location_lat'),
                    'lng' => $request->input('location_lng'),
                    'user_id' => auth()->user()->id
                ]);

                // Creare rapportino giornaliero incompleto nel caso in cui siano state aggiunte delle ore extra
                if (null !== $request->input('mq_lavorati_tot')) {

                    // Create a temporary daily report
                    //$dailyTempReportId = createTemporaryDailyReport($request->input('building_site_id'));
                    
                    // Add report rows
                    createCustomerReportRows(
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

                    //$reportIds = [$dailyTempReportId];
                    $reportIds = [$report->id];
                }

                // If we don't have reportRowIds, we set the reportRowIds to the input gotten from the form (customer report form)
                if (!isset($reportIds)) {
                    $reportIds = $request->input('report_id');
                }

                // Salvare quali rapportini sono stati utilizzati come parte integrante di questo rapporto fine cantiere
                if (null !== $reportIds and count(array_filter($reportIds)) > 0) {
                    Report::whereIn('id', $reportIds)
                        ->update([
                            'signed_off_by_report_id' => $report->id
                        ]);
                }

                // Set the toast notification
                \request()->session()->flash('toast-class', 'success');
                \request()->session()->flash('toast', 'Foglio di fine cantiere creato correttamente.');

            } catch (\Exception $e) {
                $strRandId = $this->generateErrorIdentifier();
                Log::error($strRandId . ' - Errore durante la creazione di un customer report: '. $e->getMessage());

                // Set the toast notification
                \request()->session()->flash('toast-class', 'error');
                \request()->session()->flash('toast', 'Errore durante la creazione del foglio di fine cantiere. Rif errore: ' . $strRandId);
            }

            return redirect()->route('building-sites.index');
        }

        /**
         * Display the specified resource.
         *
         * @param  \App\CustomerReport $customerReport
         * @return \Illuminate\Http\Response
         */
        public function show(CustomerReport $customerReport)
        {
            $buildingSite = $customerReport->buildingSite()->first();

            $reportsIds = $customerReport->reports()
                ->get()
                ->pluck('id')
                ->toArray();

            // Count for strutture sabbiate
            $sabb = $customerReport->customerReportWorks($reportsIds, 'S');
            $totSabbiato = 0;
            foreach($sabb as $s) {
                $totSabbiato += ($s->qty * $s->mq_lavorati_tot);
            }

            // Count for strutture verniciate
            $vern = $customerReport->customerReportWorks($reportsIds, 'V');
            $totVerniciato = 0;
            foreach($vern as $v) {
                $totVerniciato += ($v->qty * $v->mq_lavorati_tot);
            }

            // Count for strutture verniciate
            $other = $customerReport->customerReportWorks($reportsIds);
            $totAltro = 0;
            foreach($other as $o) {
                $totAltro += ($o->qty * $o->mq_lavorati_tot);
            }

            return view('backend.reports.view-foglio-fine-cantiere', compact('customerReport', 'buildingSite', 'totSabbiato', 'totVerniciato', 'totAltro'));
        }


        /**
         * Shows the customer report edit screen
         *
         * @param CustomerReport $customerReport
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function edit(CustomerReport $customerReport)
        {
            if ($this->authorize('update', $customerReport)) {

                $buildingSite = $customerReport->buildingSite()->first();
                
                $reportsIds = $customerReport->reports()
                ->get()
                ->pluck('id')
                ->toArray();

                // Count for strutture sabbiate
                $sabb = $customerReport->customerReportWorks($reportsIds, 'S');
                $totSabbiato = 0;
                foreach($sabb as $s) {
                    $totSabbiato += ($s->qty * $s->mq_lavorati_tot);
                }

                // Count for strutture verniciate
                $vern = $customerReport->customerReportWorks($reportsIds, 'V');
                $totVerniciato = 0;
                foreach($vern as $v) {
                    $totVerniciato += ($v->qty * $v->mq_lavorati_tot);
                }

                // Count for strutture verniciate
                $other = $customerReport->customerReportWorks($reportsIds);
                $totAltro = 0;
                foreach($other as $o) {
                    $totAltro += ($o->qty * $o->mq_lavorati_tot);
                }
                
                return view('backend.reports.foglio-fine-cantiere', compact('customerReport', 'buildingSite', 'reportsIds', 'totSabbiato', 'totVerniciato', 'totAltro'));
            }
        }


        /**
         * Function used to update a customer report row
         *
         * @param Request $request
         * @param CustomerReport $customerReport
         * @return \Illuminate\Http\RedirectResponse
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function update(Request $request, CustomerReport $customerReport)
        {
            if ($this->authorize('update', $customerReport)) {

                $request->validate([
                    'company_name' => 'required',
                    'company_address' => 'required',
                    'company_city' => 'required',
                    'billing_to' => 'required|in:cliente,azienda terza',
                    'job_type' => 'required|in:a corpo,a consuntivo,ad euro/mq',
                    'work_description' => 'required',
                    'employee_name' => 'required',
                ]);

                try {
                    $customerReport->company_name = $request->input('company_name');

                    $customerReport->company_address = $request->input('company_address');

                    $customerReport->company_city = $request->input('company_city');

                    $customerReport->billing_to = $request->input('billing_to');

                    $customerReport->billing_to_company = null;
//                    $customerReport->billing_to_company = $request->input('billing_to_company');

                    $customerReport->job_type = $request->input('job_type');

                    $customerReport->work_description = $request->input('work_description');

                    $customerReport->signature_name = $request->input('signature_name');

                    $customerReport->signature_company_name = $request->input('signature_company_name');

                    $customerReport->employee_name = $request->input('employee_name');

                    $customerReport->additional_notes = $request->input('additional_notes');

                    $customerReport->updated_by = auth()->user()->name . ' ' . auth()->user()->surname;
                    
                    $customerReport->save();
                    
                    // Add report rows
                    
                    if (null !== $request->input('mq_lavorati_tot')) {
                        createCustomerReportRows(
                            $customerReport->id,
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
                    \request()->session()->flash('toast', 'Foglio di fine cantiere aggiornato correttamente.');

                } catch (\Exception $e) {
                    $strRandId = $this->generateErrorIdentifier();
                    Log::error($strRandId . ' - Errore durante l\'aggiornamento di un customer report: '. $e->getMessage());

                    // Set the toast notification
                    \request()->session()->flash('toast-class', 'error');
                    \request()->session()->flash('toast', 'Errore durante l\'aggiornamento del foglio di fine cantiere. Rif errore: ' . $strRandId);
                }

                return redirect()->route('customer_report_list');
            }
        }


        /**
         * Delete customer report functionality
         *
         * @param CustomerReport $customerReport
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function destroy(CustomerReport $customerReport)
        {
            if ($this->authorize('delete', $customerReport)) {
                try {

                    $ts = Carbon::now()->toDateTimeString();
                    $data = array('deleted_at' => $ts, 'updated_by' => auth()->user()->name . ' ' . auth()->user()->surname);

                    $customerReport->update($data);

                } catch (\Exception $e) {
                    $strRandId = $this->generateErrorIdentifier();
                    Log::error($strRandId . ' - Errore durante l\'eliminazione di un customer report: '. $e->getMessage());

                    // Set the toast notification
                    \request()->session()->flash('toast-class', 'error');
                    \request()->session()->flash('toast', 'Errore durante l\'eliminazione del foglio di fine cantiere. Rif errore: ' . $strRandId);
                }
            }
        }


        /**
         * Generates a customer report PDF file that the user can download
         *
         * @param CustomerReport $customerReport
         * @return mixed
         */
        public function downloadPdf(CustomerReport $customerReport)
        {
            // Create a PDF document for the customer report
            try {
                // Get the building site id
                $buildingSite = BuildingSite::find($customerReport->building_site_id);

                // Get all the dayily reports linked to this customer report
                $reportIds = $customerReport->reports()
                    ->pluck('id')
                    ->toArray();

                // Count for strutture sabbiate
                $sabb = $customerReport->customerReportWorks($reportIds, 'S');
                $totSabbiato = 0;
                foreach($sabb as $s) {
                    $totSabbiato += ($s->qty * $s->mq_lavorati_tot);
                }

                // Count for strutture verniciate
                $vern = $customerReport->customerReportWorks($reportIds, 'V');
                $totVerniciato = 0;
                foreach($vern as $v) {
                    $totVerniciato += ($v->qty * $v->mq_lavorati_tot);
                }

                // Count for strutture lavaggio
                $lavaggio = $customerReport->customerReportWorks($reportIds, 'L');
                $totLavato = 0;
                foreach($lavaggio as $l) {
                    $totLavato += ($l->qty * $l->mq_lavorati_tot);
                }

                // Count for strutture soffiatura
                $soffiatura = $customerReport->customerReportWorks($reportIds, 'SOFF');
                $totSoffiato = 0;
                foreach($soffiatura as $sf) {
                    $totSoffiato += ($sf->qty * $sf->mq_lavorati_tot);
                }

                // Count for strutture intonaco
                $intonaco = $customerReport->customerReportWorks($reportIds, 'I');
                $totIntonacato = 0;
                foreach($intonaco as $i) {
                    $totIntonacato += ($i->qty * $i->mq_lavorati_tot);
                }

                // Count for strutture verniciate
                $other = $customerReport->customerReportWorks($reportIds);
                $totAltro = 0;
                foreach($other as $o) {
                    $totAltro += ($o->qty * $o->mq_lavorati_tot);
                }

                $cstName = Str::slug(strtolower(str_replace('.', '', $buildingSite->customer->company_name)));
                $fileName = "{$customerReport->transformDateField('created_at', 'd-m-Y')}-{$cstName}-fine-cantiere.pdf";

//                return view('backend.PDF.customer-signature-pdf', compact('customerReport', 'totSabbiato', 'totVerniciato', 'totAltro'));
                $pdf = PDF::loadView(
                    'backend.PDF.customer-signature-pdf',
                    compact('customerReport', 'totSabbiato', 'totVerniciato', 'totLavato', 'totSoffiato', 'totIntonacato', 'totAltro')
                );

                return $pdf->stream("$fileName");


            } catch (\Exception $e) {
                Log::error('Errore durante l\'invio della email al cliente per un customer report: '. $e->getMessage());
                abort(500);
            }
        }

    
        public function downloadAllPdf(Request $request) {
                $startDate = $request->get('date_from');
                $endDate = $request->get('date_to');
                $buildingSiteIdFilter = $request->get('building_site_id');
                $employeeIdFilter = $request->get('eid');

                // Lista dei dipendenti
                $user = new User();
                $employees = $user->getEmployeeList(false, true);

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


                // Get a count of the monthly report
                $reports = CustomerReport::whereBetween('created_at',
                    [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')])
                    ->whereHas('employee')
                    ->whereHas('buildingSite')
                    ->where(function ($q) use ($buildingSiteIdFilter, $employeeIdFilter) {
                        if (null !== $buildingSiteIdFilter) {
                            $q->where('building_site_id', '=', $buildingSiteIdFilter);
                        }

                        if (null !== $employeeIdFilter) {
                            $q->where('user_id', '=', $employeeIdFilter);
                        }
                    })
                    ->orderBy('id', 'desc')
                    ->get();
                    
                return createAllPdfReportDownload($reports);    

        }       

    }
    
