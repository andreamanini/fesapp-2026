<?php

    namespace App;

    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Support\Facades\DB;

    class Report extends Model
    {

        use SoftDeletes;

        /**
         * @var array
         */
        protected $guarded = ['id'];

        /**
         * @var array
         */
        public $monthNames = [
            'it' => [
                'Gennaio',
                'Febbraio',
                'Marzo',
                'Aprile',
                'Maggio',
                'Giugno',
                'Luglio',
                'Agosto',
                'Settembre',
                'Ottobre',
                'Novembre',
                'Dicembre'
            ],

            'en' => [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'Dicember'
            ],
        ];

        /**
         * @var array
         */
        public $dayNames = [
            'it' => [
                'Lunedi',
                'Martedi',
                'Mercoledi',
                'Giovedi',
                'Venerdi',
                'Sabato',
                'Domenica',
            ],

            'en' => [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday',
            ],
        ];

        /**
         * @var array
         */
        protected $workTypes = [
            'S' => 'Sabbiatura',
            'V' => 'Verniciatura',
            'VA' => 'Verniciatura anticorrosiva',
            'VC' => 'Verniciatura da carrozzeria',
            'VIM' => 'Verniciatura impregnante',
            'VIN' => 'Verniciatura intumescente',
            'L' => 'Lavaggio',
            'SOFF' => 'Soffiatura',
            'I' => 'Intonaco',
            'II' => 'Intonaci intumescenti',
            'ALTRO' => 'Altro'
        ];

        
        /**
         * @var array
         */
        protected $workTypeFieldNames = [
            'S' => 'strutt_sabbiata',
            'V' => 'strutt_verniciata',
            'L' => 'strutt_lavaggio',
            'SOFF' => 'strutt_soffiatura',
            'I' => 'strutt_intonaco',
            'VA' => 'strutt_verniciata_anticorrosiva',
            'VC' => 'strutt_verniciata_carrozzeria',
            'VIM' => 'strutt_verniciata_impregnante',
            'VIN' => 'strutt_verniciata_intumescente',
            'II' => 'strutt_intonaci_intumescenti',
            'ALTRO' => 'strutt_altro'
        ];
        
        /**
         * Retrieves all the report rows linked to this report
         *
         * @param string|null $rowTypeFilter
         * @return $this
         */
        public function rows(?string $rowTypeFilter = null)
        {
            return $this->hasMany(ReportRow::class)
                ->where(function($q) use($rowTypeFilter) {
                    if (null !== $rowTypeFilter) {
                        $q->where('work_type', '=', $rowTypeFilter);
                    } else if ('' === $rowTypeFilter) {
                        $q->whereNull('work_type');
                    }

                });
        }

        /**
         * Retrieves the user that generated this report
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function employee()
        {
            return $this->belongsTo(User::class, 'user_id');
        }

        /**
         * Retrieves the building site that is linked to this report
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function buildingSite()
        {
            return $this->belongsTo(BuildingSite::class);
        }

        /**
         * Returns the sum of the mq_lavorati_tot field times the quantity for each row assigned to a specific report
         *
         * @return \Illuminate\Database\Eloquent\Collection
         */
        public function countMq()
        {
            $query = $this->rows()->select(DB::raw('SUM(mq_lavorati_tot * IFNULL(qty,1)) as tot_worked_mq'))
                ->first();

            return (!empty($query->tot_worked_mq) ? $query->tot_worked_mq : 0);
        }

        /**
         * Retrieves statistics based on the sum building site, customer id or employee id
         *
         * @param int|null $buildingSiteId
         * @param int|null $customerId
         * @param int|null $employeeId
         * @return object
         */
        public function calculateStats(?int $buildingSiteId = null, ?int $customerId = null, ?int $employeeId = null)
        {
            if (null !== $buildingSiteId or null !== $employeeId) {

                $query1 = DB::table('reports')
                    ->leftJoin('report_rows', 'reports.id', '=', 'report_rows.report_id')
                    ->where(function($q) use ($buildingSiteId, $employeeId) {
                        if (null !== $buildingSiteId) {
                            $q->where('reports.building_site_id', $buildingSiteId);
                        } else {
                            $q->where('reports.user_id', $employeeId);
                        }
                    })
                    ->selectRaw(
                        'SUM(tot_petrol_used) as tot_petrol_used, '.
                                   'SUM(total_working_hours) as total_working_hours, '.
                                   '(SUM(mq_lavorati_tot * IFNULL(qty,1))) as tot_worked_mq')
                    ->first();

//                $query2 = DB::table('reports')
//                    ->join('report_rows', 'report_rows.report_id', '=', 'reports.id')
//                    ->where(function($q) use ($buildingSiteId, $employeeId) {
//                        if (null !== $buildingSiteId) {
//                            $q->where('reports.building_site_id', $buildingSiteId);
//                        } else {
//                            $q->where('reports.user_id', $employeeId);
//                        }
//                    })
//                    ->whereNotNull('mq_lavorati_tot')
//                    ->selectRaw('(SUM(mq_lavorati_tot*IFNULL(qty,1))) as tot_worked_mq')
//                    ->groupBy('qty')
//                    ->first();

//            } else if (null !== $customerId) {

            }

            return (object)[
                'tot_petrol_used' => (isset($query1->tot_petrol_used) ? $query1->tot_petrol_used : 0),
                'total_working_hours' => (isset($query1->total_working_hours) ? $query1->total_working_hours : 0),
                'tot_worked_mq' => (isset($query1->tot_worked_mq) ? $query1->tot_worked_mq : 0)
            ];
        }


        /**
         * @param string|null $dateFrom
         * @param string|null $dateTo
         * @param Customer|null $customer
         * @param BuildingSite|null $buildingSite
         * @return Model|null|object|static
         */
        public function calculateMonthlyHours(
            string $dateFrom = null,
            string $dateTo = null,
            ?Customer $customer = null,
            ?BuildingSite $buildingSite = null
        ) {

            $hours = DB::table('reports')
                ->join('building_sites', 'reports.building_site_id', '=', 'building_sites.id')
                ->where(function($q) use ($customer, $buildingSite) {
                    if (null !== $customer) {
                        $q->where('building_sites.customer_id', '=', $customer->id);
                    }

                    if (null !== $buildingSite) {
                        $q->where('building_sites.id', '=', $buildingSite->id);
                    }
                })
                ->whereBetween('reports.created_at', [$dateFrom, $dateTo])
                ->selectRaw('date(reports.created_at) as report_date, SUM(total_working_hours) as tot_h')
                ->groupByRaw('date(reports.created_at)')
                ->orderByRaw('date(reports.created_at)')
                ->first();

            return $hours;
        }

        /**
         * @param string|null $dateFrom
         * @param string|null $dateTo
         * @param Customer|null $customer
         * @param BuildingSite|null $buildingSite
         * @return Model|null|object|static
         */
        public function calculateMonthlyMeters(
            string $dateFrom = null,
            string $dateTo = null,
            ?Customer $customer = null,
            ?BuildingSite $buildingSite = null
        ) {
            $sqMeters = DB::table('reports')
                ->join('report_rows', 'report_rows.report_id', '=', 'reports.id')
                ->join('building_sites', 'reports.building_site_id', '=', 'building_sites.id')
                ->where(function($q) use ($customer, $buildingSite) {
                    if (null !== $customer) {
                        $q->where('building_sites.customer_id', '=', $customer->id);
                    }

                    if (null !== $buildingSite) {
                        $q->where('building_sites.id', '=', $buildingSite->id);
                    }
                })
                ->whereBetween('reports.created_at', [$dateFrom, $dateTo])
                ->selectRaw('date(reports.created_at) as report_date, (SUM(mq_lavorati_tot * IFNULL(qty,1))) as tot_mq')
                ->groupByRaw('date(reports.created_at)')
                ->orderByRaw('date(reports.created_at)')
                ->first();

            return $sqMeters;
        }

        /**
         * created_at attribute getter
         *
         * @param $value
         * @return string
         */
        public function getCreatedAtAttribute($value)
        {
            $time = new Carbon($value);
            return $time->format('d-m-Y H:i:s');
        }

        /**
         * @param string $fieldName
         * @param string $format
         * @return string
         */
        public function transformDateField(string $fieldName, string $format = 'Y-m-d H:i:s')
        {
            $time = new Carbon($this->{$fieldName});
            return $time->format($format);
        }

        /**
         * @param string|null $workType
         * @return mixed|string
         */
        public function getWorkTypeName(?string $workType = null)
        {
            return (isset($this->workTypes[$workType]) ? $this->workTypes[$workType] : 'Altro');
        }

        /**
         * @param string|null $workType
         * @return mixed|string
         */
        public function getWorkTypeFieldName(?string $workType = null)
        {
            return (isset($this->workTypeFieldNames[$workType]) ? $this->workTypeFieldNames[$workType] : 'strutt_sabbiata');
        }
        
        public function checkReportPresence(?int $user, string $date) {
            
            
            $reportIgnore = DB::table('reports_ignore')
                    ->where('user_id', $user)
                    ->whereDate('date', '=', $date)
                    ->count();
            ;
            if ($reportIgnore > 0)
                return true;
            
            $reportPresence = DB::table('reports')
                    ->where('user_id', $user)
                    ->whereDate('created_at', '=', $date)
                    ->count();

            if ($reportPresence > 0)
                return true;
            else
                return false;
        }
    }
