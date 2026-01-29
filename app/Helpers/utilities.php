<?php


    use Illuminate\Support\Str;
    use Barryvdh\DomPDF\Facade as PDF;

    /**
    * Function used to sluggify file names
    *
    * @param string $fileName
    * @param string $ext
    * @return mixed|string
    */
    function sluggifyFileName(string $fileName, string $ext)
    {
        $fileName = strtolower(Str::slug($fileName, '-'));
        $fileExt = strtolower($ext);
        $fileName = str_replace($fileExt, '', $fileName);

        return $fileName;
    }

    /**
     * Function used to translate a month name from english to italian
     *
     * @param string $month
     * @return mixed
     */
    function convertMonthName(string $month)
    {
        $r = new \App\Report();

        return str_ireplace(
            $r->monthNames['en'],
            $r->monthNames['it'],
            $month);
    }

    /**
     * Function used to translate a day name from english to italian
     *
     * @param string $day
     * @return mixed
     */
    function convertDayName(string $day)
    {
        $r = new \App\Report();

        return str_ireplace(
            $r->dayNames['en'],
            $r->dayNames['it'],
            $day);
    }

    /**
     * Function used to store daily report square meter rows
     *
     * @param int $reportId
     * @param array $mqLavoratiTotali
     * @param array|null $mqLavoratiX
     * @param array|null $mqLavoratiY
     * @param array|null $mqLavoratiZ
     * @param array|null $qty
     * @param array|null $workType
     * @param array|null $struttura
     * @param array|null $materiale
     */
    function createReportRows(int $reportId, array $mqLavoratiTotali, ?array $mqLavoratiX = null, ?array $mqLavoratiY = null,
        ?array $mqLavoratiZ = null, ?array $qty = null, ?array $workType = null, ?array $struttura = null, ?array $materiale = null)
    {

        if (null !== $mqLavoratiTotali) {

            for($rr=0; $rr<count($mqLavoratiTotali); $rr++) {

                $mqX = (!empty($mqLavoratiX[$rr]) ? $mqLavoratiX[$rr] : null);
                $mqY = (!empty($mqLavoratiY[$rr]) ? $mqLavoratiY[$rr] : null);
                $mqZ = (!empty($mqLavoratiZ[$rr]) ? $mqLavoratiZ[$rr] : null);
                $mqLavoratiTot = (!empty($mqLavoratiTotali[$rr]) ? $mqLavoratiTotali[$rr] : null);

                // Insert the report row if any one of these field is not null
                if (null !== $mqX or null !== $mqY or null !== $mqZ or null !== $mqLavoratiTot) {

                    $reportRow = \App\ReportRow::create([
                        'work_type' => $workType[$rr],
                        'strutt_sabbiata' => ((null == $workType[$rr] or 'S' == $workType[$rr]) ? $struttura[$rr] : null),
                        'strutt_verniciata' => ('V' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_lavaggio' => ('L' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_soffiatura' => ('SOFF' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_intonaco' => ('I' == $workType[$rr] ? $struttura[$rr] : null),
                        'materiale' => (!empty($materiale[$rr]) ? $materiale[$rr] : null),
                        'qty' => (!empty($qty[$rr]) ? $qty[$rr] : 1),
                        'mq_lavorati_x' => $mqX,
                        'mq_lavorati_y' => $mqY,
                        'mq_lavorati_z' => $mqZ,
                        'mq_lavorati_tot' => $mqLavoratiTot,
                        'report_id' => $reportId,
                    ]);

                }
            }
        }
    }

/**
     * Function used to store daily report square meter rows
     *
     * @param int $reportId
     * @param array $mqLavoratiTotali
     * @param array|null $mqLavoratiX
     * @param array|null $mqLavoratiY
     * @param array|null $mqLavoratiZ
     * @param array|null $qty
     * @param array|null $workType
     * @param array|null $struttura
     * @param array|null $materiale
     */
    function createCustomerReportRows(int $reportId, array $mqLavoratiTotali,?array $mqLavoratiX = null, ?array $mqLavoratiY = null,
        ?array $mqLavoratiZ = null, ?array $qty = null, ?array $workType = null, ?array $struttura = null, ?array $materiale = null)
    {

        if (null !== $mqLavoratiTotali) {

            \App\CustomerReportRow::where('customer_report_id', $reportId)->delete();
            
            for($rr=0; $rr<count($mqLavoratiTotali); $rr++) {

                $mqX = (!empty($mqLavoratiX[$rr]) ? $mqLavoratiX[$rr] : null);
                $mqY = (!empty($mqLavoratiY[$rr]) ? $mqLavoratiY[$rr] : null);
                $mqZ = (!empty($mqLavoratiZ[$rr]) ? $mqLavoratiZ[$rr] : null);
                $mqLavoratiTot = (!empty($mqLavoratiTotali[$rr]) ? $mqLavoratiTotali[$rr] : null);

                // Insert the report row if any one of these field is not null
                if (null !== $mqX or null !== $mqY or null !== $mqZ or null !== $mqLavoratiTot) {
                    $reportRow = \App\CustomerReportRow::create([
                        'work_type' => $workType[$rr],
                        'strutt_sabbiata' => ((null == $workType[$rr] or 'S' == $workType[$rr]) ? $struttura[$rr] : null),
                        'strutt_verniciata' => ('V' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_lavaggio' => ('L' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_soffiatura' => ('SOFF' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_intonaco' => ('I' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_verniciata_anticorrosiva' => ('VA' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_verniciata_carrozzeria' => ('VC' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_verniciata_impregnante' => ('VIM' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_verniciata_intumescente' => ('VIN' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_intonaci_intumescenti' => ('II' == $workType[$rr] ? $struttura[$rr] : null),
                        'strutt_altro' => ('ALTRO' == $workType[$rr] ? $struttura[$rr] : null),
                        'materiale' => (!empty($materiale[$rr]) ? $materiale[$rr] : null),
                        'qty' => (!empty($qty[$rr]) ? $qty[$rr] : 1),
                        'mq_lavorati_x' => $mqX,
                        'mq_lavorati_y' => $mqY,
                        'mq_lavorati_z' => $mqZ,
                        'mq_lavorati_tot' => $mqLavoratiTot,
                        'customer_report_id' => $reportId,
                    ]);

                }
            }
        }
    }    
    
    /**
     * Creates an incomplete report for the user that's currently logged in, this function should only be used before
     * calling the createReportRows function, in order to store square meters report rows
     *
     * @param int $buildingSiteId
     * @return mixed
     */
    function createTemporaryDailyReport(int $buildingSiteId)
    {
        // Create the temporary daily report
        $report = \App\Report::create([
            'time_end' => date('Y-m-d 18:00:00'),
            'total_working_hours' => 0,
            'employees' => json_encode([auth()->user()->name . ' '.auth()->user()->surname]),
            'job_details' => '{"job_coperture":null,"job_stuccature":null,"job_carteggiatura":null,"job_lavaggio":null,"job_sabbiatura":null,"job_verniciatura":null,"job_intonaco":null,"job_other_text":null,"job_other":null}',
            'tot_petrol_used' => 0,
            'report_type' => 'incomplete',
            'report_view' => 'employee',
            'user_id' => auth()->user()->id,
            'building_site_id' => $buildingSiteId
        ]);

        return $report->id;
    }

    /**
     * @param \App\Report $report
     * @param bool $streamPdf
     * @return \Illuminate\Http\Response
     */
    function createPdfReport(\App\Report $report, bool $streamPdf = true, string $dirName = null)
    {
        try {
            $timeStart = new \Carbon\Carbon($report->time_start);
            $timeEnd = new \Carbon\Carbon($report->time_end);

            $employee = $report->employee()->first();
            $building = App\BuildingSite::where('id', $report->building_site_id)->get()->first();
            $fileName = "{$timeStart->format('d-m-Y')}-".Str::slug($employee->name.'-'.$employee->surname.'-'.$building->site_name)."-report.pdf";

//                return view("backend.PDF.daily-report-pdf", compact('report', 'timeStart', 'timeEnd'));
            $pdf = PDF::loadView('backend.PDF.daily-report-pdf', compact('report', 'timeStart', 'timeEnd'));

            if (!$streamPdf and null !== $dirName) {
                $pdf->save(public_path('media/'.$dirName.'/'.$fileName));
            } else {
                return $pdf->stream("$fileName");
            }

        } catch (\Exception $e) {
            die($e);
            Log::error('Errore durante l\'invio della email al cliente per un customer report: '. $e->getMessage());
            abort(500);
        }
    }
    
    /**
     * @param \App\Report $report
     * @param bool $streamPdf
     * @return \Illuminate\Http\Response
     */
    function createAllPdfReport($reports)
    {
        try {
            $folder = date("Ymdhis");
            mkdir('/tmp/'.$folder);
            foreach ($reports as $report) {
                $timeStart = new \Carbon\Carbon($report->time_start);
                $timeEnd = new \Carbon\Carbon($report->time_end);

                $employee = $report->employee()->first();
                $building = App\BuildingSite::where('id', $report->building_site_id)->get()->first();
                
                $fileName = "{$timeStart->format('d-m-Y')}-".Str::slug($employee->name.'-'.$employee->surname.'-'.$building->site_name)."-report-{$report->id}.pdf";

                $pdf = PDF::loadView('backend.PDF.daily-report-pdf', compact('report', 'timeStart', 'timeEnd'));

                $pdf->save('/tmp/'.$folder.'/'.$fileName);
                
            }
            
            $zip_file = 'report.zip';
            $zip = new \ZipArchive();
            $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $path = '/tmp/'.$folder;
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $name => $file)
            {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($path) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
            
            File::deleteDirectory($path);
            
            return response()->download($zip_file);
            

        } catch (\Exception $e) {
            die($e);
            Log::error('Errore durante l\'invio della email al cliente per un customer report: '. $e->getMessage());
            abort(500);
        }
    }

    
    function checkOpenedBuildingSite($siteid){  

        return DB::table('building_sites')
             ->where('id', $siteid)
             ->value('status');
        }

    function checkUserRole($userid){
    return DB::table('users')
            ->where('id', $userid)
            ->value('role');
    }
    

    function createAllPdfReportDownload($reports)
    {
        // Create a PDF document for the customer report
        try {

            $folder = date("Ymdhis");
            mkdir('/tmp/'.$folder);
            foreach ($reports as $customerReport) {

                // Get the building site id
                $buildingSite = App\BuildingSite::find($customerReport->building_site_id);

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
                
                $cstName = Str::slug(strtolower(str_replace('.', '', $buildingSite->site_name)));
                $fileName = "{$customerReport->transformDateField('created_at', 'd-m-Y')}-{$cstName}-{$reportIds[0]}-fine-cantiere.pdf";

//              return view('backend.PDF.customer-signature-pdf', compact('customerReport', 'totSabbiato', 'totVerniciato', 'totAltro'));
                $pdf = PDF::loadView('backend.PDF.customer-signature-pdf',compact('customerReport', 'totSabbiato', 'totVerniciato', 'totLavato', 'totSoffiato', 'totIntonacato', 'totAltro'));

                $pdf->save('/tmp/'.$folder.'/'.$fileName);

            }

            $zip_file = 'fine-cantiere.zip';
            $zip = new \ZipArchive();
            $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $path = '/tmp/'.$folder;
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $name => $file)
            {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($path) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();

            File::deleteDirectory($path);

            return response()->download($zip_file);

        } catch (\Exception $e) {
            Log::error('Errore durante l\'invio della email al cliente per un customer report: '. $e->getMessage());
            abort(500);
        }
    }    