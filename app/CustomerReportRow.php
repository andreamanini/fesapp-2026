<?php

    namespace App;

    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;

    class CustomerReportRow extends Model
    {
        protected $guarded = ['id'];

        /**
         * Retrieves the report linked to this report row
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function Report()
        {
            return $this->belongsTo(CustomerReport::class);
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
            return $time->format('d-m-Y');
        }
    }
