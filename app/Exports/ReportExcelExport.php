<?php

    namespace App\Exports;

    use App\User;
    use Illuminate\Support\Facades\Request;
    use Carbon\Carbon;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;

    class ReportExcelExport implements FromCollection, WithHeadings {

        protected $data;

        /**
         * Write code on Method
         *
         * @return response()
         */

        public function __construct($data)
        {
            $this->data = $data;
        }


        /**
         * Write code on Method
         *
         * @return response()
         */
        public function collection()
        {
            return collect($this->data);
        }



        /**
         * Write code on Method
         *
         * @return response()
         */

        public function headings() :array
        {

            $request = Request::input();
            $employee = User::find($request['employee_id']);
            $dateFrom = new Carbon($request['export_month']);
            
            return [[strtoupper($employee->name) . ' ' . strtoupper($employee->surname)],["ORE DI " . strtoupper(convertMonthName($dateFrom->format('F'))) . ' ' . $dateFrom->format('Y')],[
                'GIORNO',
                'ORA INIZIO',
                'PAUSA',
                'ORA FINE',
                'VIAGGIO',
                'TRASFERTA',
                'TOTALE',
                'ORDIN',
                'STRAO',
                'SABATO',
                'DOMENICA',
                'PASTI',
                'PASTI PAG',
                'RIMBORSI'
            ]];
        }
    }
