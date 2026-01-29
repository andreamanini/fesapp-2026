<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class Customer extends Model
    {
        protected $guarded = ['id'];

        /**
         * Retrieves the building sites linked to this customer
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function buildingSites()
        {
            return $this->hasMany(BuildingSite::class);
        }

        public function scopeSearch($query, $term)
        {
            return $query->where('company_name', 'like', '%' . $term . '%');
        }

        /**
         * Returns an object with some simple stats based on the customer
         *
         * @return object
         */
        public function stats()
        {
            $query = $this->whereNotNull('created_at');


            return (object)[
                'total_customers' => $query->get()->count(),
                'active_customers' => $query->whereHas('buildingSites', function ($q) {
                    $q->where('status', 'open');
                })->get()->count()
            ];
        }

        /**
         * Retrieves all the reports for a specific customer, linking them via his building sites
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
         */
        public function reports()
        {
            return $this->hasManyThrough(Report::class, BuildingSite::class);
        }

        /**
         * Retrieves the customer reports associated to this customer
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
         */
        public function customerReports()
        {
            return $this->hasManyThrough(CustomerReport::class, BuildingSite::class);
        }

    }
