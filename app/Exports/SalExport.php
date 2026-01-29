<?php

    namespace App\Exports;

    use App\BuildingSite;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Contracts\View\View;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Maatwebsite\Excel\Concerns\FromView;
    use Maatwebsite\Excel\Events\BeforeSheet;
    use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
    use PhpOffice\PhpSpreadsheet\IOFactory;    
    use Illuminate\Support\Facades\File;

    class SalExport implements FromView
    {
        use Exportable;

        /**
         * @var BuildingSite
         */
        protected $buildingSite;

        /**
         * @var Collection
         */
        protected $reports;

        /**
         * @var Collection
         */
        protected $cstReports;

        /**
         * @var string
         */
        protected $salType;

        /**
         * @var string
         */
        protected $month;

        protected $itaMonths = [
            1 => 'Gennaio',
            2 => 'Febbraio',
            3 => 'Marzo',
            4 => 'Aprile',
            5 => 'Maggio',
            6 => 'Giugno',
            7 => 'Luglio',
            8 => 'Agosto',
            9 => 'Settembre',
            10 => 'Ottobre',
            11 => 'Novembre',
            12 => 'Dicembre',
        ];


        /**
         * SalExport constructor.
         *
         * @param BuildingSite $buildingSite
         * @param Collection $reports
         * @param string $salType
         * @param string $month
         */
        public function __construct(BuildingSite $buildingSite, Collection $reports, string $salType, $month, $year,
            ?Collection $cstReports = null, string $filename = null, string $path = null, string $zip = null)
        {
            $this->buildingSite = $buildingSite;
            
            list($yearfilter,$monthfilter,$dayfilter) = explode("-", $_POST['export_month']);
            $details = \App\Report::whereIn('building_site_id', array($buildingSite->id))->whereYear('created_at', '=', $yearfilter)->whereMonth('created_at', '=', $monthfilter)->get();

            $materials = json_decode($details[0]->materials);
            
            $reports =\App\CustomerReport::whereIn('building_site_id', array($buildingSite->id))->get();
            
            $this->reports = $reports;

            $this->cstReports = $cstReports;

            $this->salType = $salType;

            $this->month = $this->itaMonths[$month];
            
            $reader = IOFactory::createReader('Xlsx');
            if($salType == 'corpo' OR $salType == 'consuntivo')
                $spreadsheet = $reader->load('../app/public/media/sal/base_sal_corpo.xlsx');
            else
                $spreadsheet = $reader->load('../app/public/media/sal/base_sal.xlsx');
            $worksheet = $spreadsheet->getActiveSheet();
            $worksheet->getCell('I5')->setValue($buildingSite->customer->company_name);
            $taxcode = "";
            if ($buildingSite->customer->vatnumber == $buildingSite->customer->taxcode) {
                $taxcode = $buildingSite->customer->vatnumber;
            } else if ($buildingSite->customer->vatnumber != $buildingSite->customer->taxcode) {
                if ($buildingSite->customer->vatnumber != '') $taxcode .= " P.I. ".$buildingSite->customer->vatnumber;
                if ($buildingSite->customer->taxcode != '') $taxcode .= " C.F. ".$buildingSite->customer->taxcode;
            }             
            $worksheet->getCell('I6')->setValue($taxcode);
            $worksheet->getCell('I7')->setValue($buildingSite->customer->sdi);
            $worksheet->getCell('G8')->setValue($buildingSite->customer->company_name);
            $worksheet->getCell('M8')->setValue($this->month);
            $worksheet->getCell('P8')->setValue($year);
            if($salType == 'corpo')
                $worksheet->getCell('E10')->setValue('X');
            else if($salType == 'euro')
                $worksheet->getCell('J10')->setValue('X');
            else if($salType == 'consuntivo')
                $worksheet->getCell('O10')->setValue('X');
            $worksheet->getCell('K12')->setValue($buildingSite->customer->email);
            $worksheet->getCell('D14')->setValue($buildingSite->quote_number);
            $worksheet->getCell('F14')->setValue($buildingSite->quote_date);
            $worksheet->getCell('L14')->setValue($buildingSite->order_number);
            
            if($salType == 'corpo') {
                foreach($cstReports as $rep) {
                    //$worksheet->getRowDimension(14)->setRowHeight(-1);
                    $worksheet->getCell('D16')->setValue("!! ". str_replace(array("&nbsp;"),array(""),strip_tags($rep->work_description))." !!");
                }
            } else if($salType == 'euro') {
                foreach($reports as $rep) {
                    foreach($rep->rows()->get() as $row) {
                        
                        $workText = $rep->getWorkTypeName($row->work_type) . ' ' . $row->{$rep->getWorkTypeFieldName($row->work_type)};

                        $materiale = (!empty($row->materiale) ? ' Materiale ' . $row->materiale : '');
                        $mq = ' QTA ' . $row->qty . ' PER MT ' .
                              (!empty($row->mq_lavorati_x) ? $row->mq_lavorati_x : '1') . ' X ' .
                              (!empty($row->mq_lavorati_y) ? $row->mq_lavorati_y : '1') . ' X ' .
                              (!empty($row->mq_lavorati_z) ? $row->mq_lavorati_z : '1');
                        }
                        if (isset($row->mq_lavorati_tot)) {
                            $worksheet->getCell('E17')->setValue(($workText ?? ''). $materiale . $mq);
                            $worksheet->getCell('E18')->setValue("TOT. MQ:");
                            $worksheet->getCell('E19')->setValue($row->mq_lavorati_tot);
                        }
                }
                
                
            } else if($salType == 'consuntivo') {
                foreach($cstReports as $rep) {
                    $worksheet->getCell('D16')->setValue("!! ". str_replace(array("&nbsp;","\n","\r"),array("","",""),strip_tags($rep->work_description))." !!");
                }
            }
            
            
            // aggiungo foglio per i dettagli
            $spreadsheet->setActiveSheetIndex(1);
            $worksheet2 = $spreadsheet->getActiveSheet();
            // metto tutti i metri quadrati inseriti;
            $i = 2;
            $totmq = 0;
            foreach($reports as $rep) {
                foreach($rep->rows()->get() as $row) {
                    if (isset($row->mq_lavorati_tot)) {
                        $worksheet2->getCell('A'.$i)->setValue($rep->getWorkTypeName($row->work_type) ?? '');
                        $worksheet2->getCell('B'.$i)->setValue($row->{$rep->getWorkTypeFieldName($row->work_type)} ?? '');
                        $worksheet2->getCell('C'.$i)->setValue($row->materiale);
                        $worksheet2->getCell('D'.$i)->setValue($row->qty);
                        $worksheet2->getCell('E'.$i)->setValue((!empty($row->mq_lavorati_x) ? $row->mq_lavorati_x : '1') . ' X ' .
                          (!empty($row->mq_lavorati_y) ? $row->mq_lavorati_y : '1') . ' X ' .
                          (!empty($row->mq_lavorati_z) ? $row->mq_lavorati_z : '1'));
                        $worksheet2->getCell('F'.$i)->setValue($row->mq_lavorati_tot);
                        $totmq = $totmq + $row->mq_lavorati_tot;
                        $i++;
                    }
                }    
            }
            if ($i>2) {
                $worksheet2->getCell('E'.$i)->setValue("TOTALE MQ");
                $worksheet2->getCell('F'.$i)->setValue($totmq);
            }
            
            
                        
            // aggiungo foglio per i materiali
            $spreadsheet->setActiveSheetIndex(2);
            $worksheet3 = $spreadsheet->getActiveSheet();
            
            foreach ($details as $key => $detail) {
                $worksheet3->getCell('A'.($key+2))->setValue($detail->truck_driver_name);
                $worksheet3->getCell('B'.($key+2))->setValue($detail->time_start);
                $worksheet3->getCell('C'.($key+2))->setValue($detail->time_end);
                $worksheet3->getCell('D'.($key+2))->setValue($detail->total_working_hours);
                $worksheet3->getCell('E'.($key+2))->setValue($detail->travel_time);
                $worksheet3->getCell('F'.($key+2))->setValue($detail->tot_petrol_used);
                
                $materials = json_decode($detail->materials);
                
                if (@isset($materials->materials_km_giornalieri)) $worksheet3->getCell('G'.($key+2))->setValue($materials->materials_km_giornalieri);
                if (@isset($materials->materials_diluente)) $worksheet3->getCell('H'.($key+2))->setValue($materials->materials_diluente);
                if (@isset($materials->materials_intonacatrici)) $worksheet3->getCell('I'.($key+2))->setValue($materials->materials_intonacatrici);
                if (@isset($materials->materials_big_bag)) $worksheet3->getCell('J'.($key+2))->setValue($materials->materials_big_bag);
                if (@isset($materials->materials_sacchi)) $worksheet3->getCell('K'.($key+2))->setValue($materials->materials_sacchi);
                if (@isset($materials->materials_latte)) $worksheet3->getCell('L'.($key+2))->setValue($materials->materials_latte);
                if (@isset($materials->materials_other)) $worksheet3->getCell('M'.($key+2))->setValue($materials->materials_other);
                
                $worksheet3->getCell('N'.($key+2))->setValue($detail->extra_expenses);
            }

            
            
            // rendo attivo il primo foglio
            $spreadsheet->setActiveSheetIndex(0);
            
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');  

            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            //$writer->save($path.$filename.".xls");
            if ($zip === null) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
                $writer->save('php://output');
                die;
            } else {
                $writer->save($path.$filename.".xlsx");
            }
        }


        /**
         * @return View
         */
        public function view(): View
        {
            return view('backend.SAL.sal-lavori', [
                'reports' => $this->reports,
                'cstReports' => $this->cstReports,
                'buildingSite' => $this->buildingSite,
                'salType' => $this->salType,
                'month' => $this->month,
            ]);
        }

        /**
         * @return array
         */
        public function registerEvents(): array {
            return [
                BeforeSheet::class => function (BeforeSheet $event) {
                    $event->sheet
                        ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

//                    $event->sheet
//                        ->getPageSetup()
//                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                },
            ];
        }
    }
