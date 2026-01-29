<?php

    namespace App;

    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class CustomerReport extends Model
    {
        use SoftDeletes;

        protected $guarded = ['id'];
        
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
         * @return Model|null|object|static
         */
        public function buildingSite()
        {
            return $this->belongsTo(BuildingSite::class);
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function reports()
        {
            return $this->hasMany(Report::class, 'signed_off_by_report_id');
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
         * Retrieves the customer for this report
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function customer()
        {
            return $this->belongsTo(Customer::class);
        }

        /**
         * @param array|null $reportIds
         * @param string $workType
         * @return mixed
         */
        public function customerReportWorks(array $reportIds = null, ?string $workType = null)
        {
            if (null == $reportIds) {
                $reportIds = $this->reports()
                    ->get()
                    ->pluck('id');
            }

            return ReportRow::whereIn('report_id', $reportIds)
                ->where(function($q) use ($workType) {
                    if (null == $workType) {
                        $q->whereNull('work_type');
                    } else {
                        $q->where('work_type', '=', $workType);
                    }
                })
                ->get();
        }
        
        /**
         * Retrieves all the report rows linked to this report
         *
         * @param string|null $rowTypeFilter
         * @return $this
         */
        public function rows(?string $rowTypeFilter = null)
        {
            return $this->hasMany(CustomerReportRow::class)
                ->where(function($q) use($rowTypeFilter) {
                    if (null !== $rowTypeFilter) {
                        $q->where('work_type', '=', $rowTypeFilter);
                    } else if ('' === $rowTypeFilter) {
                        $q->whereNull('work_type');
                    }

                });
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

    }
