<?php

    namespace App\Http\Controllers;

    use App\BuildingSite;
    use App\Customer;
    use App\CustomerReport;
    use App\Exports\ReportExport;
    use App\Exports\SalExport;
    use App\Jobs\ProcessReport;
    use App\Machinery;
    use App\Report;
    use App\User;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;
    use Maatwebsite\Excel\Facades\Excel;
    use Symfony\Component\Process\Process;
    use Illuminate\Support\Facades\File;


    class HomeController extends Controller  
    {
        /**
         * Create a new controller instance.
         *
         * @return void
         */
        public function __construct()
        {
            $this->middleware('auth');
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function setDashboardYear(Request $request)
        {
            if ($request->session()->has('dashboard_year')) {
                $request->session()->forget('dashboard_year');
            } else {
                $request->session()->put('dashboard_year', date('Y')-1);
            }

            return redirect()->route('dashboard');
        }
        
        public function setDashboardYear2(Request $request)
        {
           
                $request->session()->put('dashboard_year', date('Y')-2);

            return redirect()->route('dashboard');
        }
        
        public function setDashboardYear3(Request $request)
        {
           
                $request->session()->put('dashboard_year', date('Y')-3);

            return redirect()->route('dashboard');
        }

        /**
         * Displays the platform dashboard
         *
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function dashboard(Request $request)
        {
            // Redirect the user if not an admin
            if (!auth()->user()->isAdmin()) {
                return redirect()->route('building-sites.index');
            }

            $check = auth()->user()->active;
            if($check == 0){
                //forzo logout se l'utente è disattivato
                Auth::logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return redirect('/');
            }
            //die
            //die;

            $bs = new BuildingSite();
            $bsStats = $bs->stats();

            $cst = new Customer();
            $cstStats = $cst->stats();
            $activeCustomers = round(($cstStats->active_customers / $cstStats->total_customers) * 100, 2);

            // Display all the stats based on the month selected (if any)
            $month = (null !== $request->get('month') ? $request->get('month') : date('m'));

            if ($request->session()->has('dashboard_year')) {
                $year = $request->session()->get('dashboard_year');
            } else {
                $year = date('Y');
            }

            $dateFrom = $year . '-' . $month . '-01 00:00:00';
            $dateTo = $year . '-' . $month . '-31 23:59:59';

            // Get a count of the monthly report
            $reports = Report::where('report_type', 'daily')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->get();
            $reportIds = $reports->pluck('id')->toArray();

            // Get the customer monthly reports
            $cstReports = CustomerReport::whereBetween('created_at', [$dateFrom, $dateTo])
                ->get();

            // Get a count of the monthly building site notes
            $notes = DB::table('notes')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->get();

            // Get a count of the monthly total liters of gasoline used
            $gasolineLt = DB::table('reports')
                ->whereIn('id', ($reports->count() > 0 ? $reportIds : []))
                ->selectRaw('SUM(tot_petrol_used) as tot_petrol_used')
                ->first();

            
            $customer_reportIds = $cstReports->pluck('id')->toArray();
            
            // Get a count of the monthly square meters worked
            $sqMeters = DB::table('customer_reports')
                ->join('customer_report_rows', 'customer_report_rows.customer_report_id', '=', 'customer_reports.id')
                ->whereIn('customer_reports.id', ($cstReports->count() > 0 ? $customer_reportIds : []))
                ->selectRaw('(SUM(mq_lavorati_tot)) as tot_mq')
                ->first();

            // Get a count of the monthly hours
            $monthlyHours = DB::table('reports')
                ->whereIn('reports.id', ($reports->count() > 0 ? $reportIds : []))
                ->selectRaw('SUM(total_working_hours) as tot_h')
                ->first();

            $dashboardStats = (object)[
                'monthlyReports' => $reports->count(),
                'gasolineLt' => ((isset($gasolineLt->tot_petrol_used) and null !== $gasolineLt->tot_petrol_used) ? $gasolineLt->tot_petrol_used : 0),
                'totMq' => ((isset($sqMeters->tot_mq) and null !== $sqMeters->tot_mq) ? $sqMeters->tot_mq : 0),
                'totHours' => ((isset($monthlyHours->tot_h) and null !== $monthlyHours->tot_h) ? $monthlyHours->tot_h : 0),
                'monthlyNotes' => $notes->count()
            ];

            return view('backend.dashboard', compact('bsStats', 'cstStats', 'activeCustomers', 'dashboardStats', 'month', 'cstReports', 'year'));
        }

        /**
         * User logout function
         *
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
         */
        public function logout(Request $request)
        {
            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect('/');
        }

        /**
         * Search functionality
         *
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function searchResults(Request $request)
        {
            $searchWord = strtolower($request->get('s'));

            if (null !== $searchWord) {

                if (!auth()->user()->isAdmin()) {
                    // Search for building sites
                    $buildingSites = BuildingSite::where('site_name', 'like', "%{$searchWord}%")
                        ->whereHas('employees', function($qq) {
                            $qq->where('users.id', '=', auth()->user()->id)->orWhere('manager_id', '=', auth()->user()->id);
                        })
                        ->get();
                    
                } else {
                    // Search for building sites
                    $buildingSites = BuildingSite::where('site_name', 'like', "%{$searchWord}%")
                        ->orWhere('address', 'like', "%{$searchWord}%")
                        ->orWhereHas('customer', function ($q) use ($searchWord) {
                            $q->where('customers.company_name', 'like', "%{$searchWord}%");
                        })
                        ->get();
                }    

                // Search for employees
                $employees = User::where('name', 'like', "%{$searchWord}%")
                    ->orWhere('surname', 'like', "%{$searchWord}%")
                    ->orWhere('email', 'like', "%{$searchWord}%")
                    ->where('id', '!=', auth()->user()->id)
                    ->get();

                // Search for machinery
                $machinery = Machinery::where('machine_name', 'like', "%{$searchWord}%")
                    ->orWhere('machine_number', 'like', "%{$searchWord}%")
                    ->orWhere('machine_description', 'like', "%{$searchWord}%")
                    ->get();

                // Search for customers
                $customers = Customer::where('company_name', 'like', "%{$searchWord}%")
                    ->orWhere('address', 'like', "%{$searchWord}%")
                    ->orWhere('email', 'like', "%{$searchWord}%")
                    ->get();
                    
                

            } else {

                $buildingSites = null;
                $employees = null;
                $machinery = null;
                $customers = null;
            }

            return view('backend.search-result', compact('searchWord', 'buildingSites', 'employees', 'machinery', 'customers'));
        }

        /**
         * Function used to generate the SALs for a specific building site
         *
         * @param Request $request
         * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
         */
        public function generateSal(Request $request)
        {
            $request->validate([
                'export_month' => 'required',
                'customer_id' => 'required|exists:customers,id',
                'building_site_id' => 'required|exists:building_sites,id',
            ]);

            try {
                
                // Get a carbon date for the beginning and the end of the selected month
                $date = new Carbon($request->input('export_month'));
  
                $dateFrom = $date->format('Y-m-d 00:00:00');
                $dateTo = $date->endOfMonth()->format('Y-m-d 23:59:59');
                
                //$customer = Customer::find($request->input('customer_id'));
                

                // Get the building site
                $buildingSite = BuildingSite::find($request->input('building_site_id'));
                $cstName = Str::slug($buildingSite->customer->company_name).'-'.$buildingSite->customer->id;
                $bsNameSlug = Str::slug($buildingSite->site_name) . '-' . $date->format('m-Y-') . $buildingSite->id;

                // Prepare the directory
                $salDirectory = "sal/{$date->format('m-Y')}/{$cstName}";

                // Prendere i rapportini di fine cantiere dividendoli per tipologia lavori (CORPO, CONSUNTIVO, €/MQ)
                $cstReportsCorpo = $buildingSite->customerReports()
                    ->whereBetween('customer_reports.created_at', [$dateFrom, $dateTo])
                    ->where('job_type', '=', 'a corpo')
                    ->orderBy('customer_reports.created_at', 'asc')
                    ->get();
                $reportsCorpo = Report::whereIn('signed_off_by_report_id', $cstReportsCorpo->pluck('id')->toArray())
                    ->get();


                // Consuntivo
                $cstReportsConsuntivo = $buildingSite->customerReports()
                    ->whereBetween('customer_reports.created_at', [$dateFrom, $dateTo])
                    ->where('job_type', '=', 'a consuntivo')
                    ->orderBy('customer_reports.created_at', 'asc')
                    ->get();
                $reportsConsuntivo = Report::whereIn('signed_off_by_report_id', $cstReportsConsuntivo->pluck('id')->toArray())
                    ->get();

                // Euro mq
                $cstReportsEuroMq = $buildingSite->customerReports()
                    ->whereBetween('customer_reports.created_at', [$dateFrom, $dateTo])
                    ->where('job_type', '=', 'ad euro/mq')
                    ->orderBy('customer_reports.created_at', 'asc')
                    ->get();
                $reportsEuroMq = Report::whereIn('signed_off_by_report_id', $cstReportsEuroMq->pluck('id')->toArray())
                    ->get();

                // Loop fine cantiere lavori a CORPO
                if ($cstReportsCorpo->count() > 0 and $reportsCorpo->count() > 0) {
                    // TODO: Salvataggio dei PDF dei rapportini ??

                    // Generazione del SAL corrispettivo per la tipologia di fine cantiere
                    //(new SalExport($buildingSite, $reportsCorpo, 'corpo', $date->format('n'), $cstReportsCorpo))
                    //    ->store($salDirectory."/sal-corpo-{$bsNameSlug}.xls", 'media');
                    (new SalExport($buildingSite, $reportsCorpo, 'corpo', $date->format('n'),$date->format('Y'),$cstReportsCorpo,"sal-corpo-{$bsNameSlug}","../app/public/media/".$salDirectory."/"));
    
                }


                // Loop fine cantiere lavori a CONSUNTIVO
                if ($cstReportsConsuntivo->count() > 0 and $reportsConsuntivo->count() > 0) {
                    // TODO: Salvataggio dei PDF dei rapportini ??

                    // Generazione del SAL corrispettivo per la tipologia di fine cantiere
                    (new ReportExport($buildingSite, $reportsConsuntivo))
                        ->store($salDirectory."/indice-consuntivo-{$bsNameSlug}.xlsx", 'media',\Maatwebsite\Excel\Excel::XLSX);

                    //(new SalExport($buildingSite, $reportsConsuntivo, 'consuntivo', $date->format('n'), $cstReportsConsuntivo))
                    //    ->store($salDirectory."/sal-consuntivo-{$bsNameSlug}.xls", 'media');
                    (new SalExport($buildingSite, $reportsConsuntivo, 'consuntivo', $date->format('n'),$date->format('Y'),$cstReportsConsuntivo,"sal-consuntivo-{$bsNameSlug}","../app/public/media/".$salDirectory."/","zip"));
   
                    // Stream a zip file to the user so he gets prompted with a download window
                    $zip = new \ZipArchive();
                    $zipName = 'sal'.date('his').'.zip';
                    $zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

                    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(public_path("media/{$salDirectory}")));
                    foreach ($files as $name => $file)
                    {
                        // We're skipping all subfolders
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();

                            // extracting filename with substr/strlen
                            $relativePath = $cstName . '/' . substr($filePath, strlen(public_path("media/{$salDirectory}")) + 1);

                            $zip->addFile($filePath, $relativePath);
                        }
                    }
                    $zip->close();

                    return response()->download($zipName)->deleteFileAfterSend(true);
                    
                        
                }


                // Loop fine cantiere lavori a EURO/MQ
                if ($cstReportsEuroMq->count() > 0 and $reportsEuroMq->count() > 0) {
                    // TODO: Salvataggio dei PDF dei rapportini ??

                    // Generazione del SAL corrispettivo per la tipologia di fine cantiere
                    (new SalExport($buildingSite, $reportsEuroMq, 'euro', $date->format('n'),$date->format('Y'),null,"sal-euro-mq-{$bsNameSlug}","../app/public/media/".$salDirectory."/"));
                    
                }

//                return Excel::download(new SalExport($buildingSite, $reports), 'sal-export.xls');

                // Create a PDF copy of the daily report via a dispatched job in order to allow the download at a later stage
//                foreach($reports as $report) {
//                    ProcessReport::dispatch($report, '');
//                }

                /*
                // Delete all previously generated SAL zip files
                array_map('unlink', glob('sal*.zip'));

                // Stream a zip file to the user so he gets prompted with a download window
                $zip = new \ZipArchive();
                $zipName = 'sal'.date('his').'.zip';
                $zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(public_path("media/{$salDirectory}")));
                foreach ($files as $name => $file)
                {
                    // We're skipping all subfolders
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();

                        // extracting filename with substr/strlen
                        $relativePath = $cstName . '/' . substr($filePath, strlen(public_path("media/{$salDirectory}")) + 1);

                        $zip->addFile($filePath, $relativePath);
                    }
                }
                $zip->close();

                return response()->download($zipName);*/

            } catch (\Exception $e) {
                die($e);
                $strRandId = $this->generateErrorIdentifier();
                Log::error($strRandId . ' - Errore durante la creazione del SAL. Rif errore: '. $e->getMessage());

                // Set the toast notification
                \request()->session()->flash('toast-class', 'error');
                \request()->session()->flash('toast', 'Errore durante la creazione del SAL. Rif errore: ' . $strRandId);
            }

        }

    }
