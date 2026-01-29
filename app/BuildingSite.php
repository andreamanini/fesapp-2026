<?php

    namespace App;

    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Support\Facades\DB;

    class BuildingSite extends Model
    {
        use SoftDeletes;

        protected $guarded = ['id'];

        protected $siteTypes = [
            'Lavaggio',
            'Sabbiatura',
            'Verniciatura',
            'Verniciatura anticorrosiva',
            'Verniciatura da carrozzeria',
            'Verniciatura impregnante',
            'Verniciatura intumescente',
            'Soffiatura',
            'Intonaci intumescenti',
            'Intonaco',
            'Altro'
        ];

        /**
         * Retrieves the customer for this building site
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function customer()
        {
            return $this->belongsTo(Customer::class);
        }

        /**
         * Returns the array of siteTypes stored against this model
         *
         * @return array
         */
        public function getSiteTypes()
        {
            return $this->siteTypes;
        }

        /**
         * site_type model attribute accessor, it will convert the site_type field in an array of values contained
         * in the json
         *
         * @param $value
         * @return array|mixed
         */
        public function getSiteTypeAttribute($value)
        {
            return (null !== $value ? json_decode($value) : []);
        }

        /**
         * address attribute accessor, it will convert the field in an array of values contained in the json
         *
         * @param $value
         * @return array|mixed
         */
        public function getAddressAttribute($value)
        {
            return (null !== $value ? json_decode($value) : []);
        }

        /**
         * updated_at accessor
         *
         * @param $value
         * @return string
         */
        public function getUpdatedAtAttribute($value)
        {
            $date = new Carbon($value);

            return $date->format('d-m-Y H:i:s');
        }

        /**
         * quote_date accessor
         *
         * @param $value
         * @return string
         */
        public function getQuoteDateAttribute($value)
        {
            if (null == $value) {
                return null;
            }

            $date = new Carbon($value);

            return $date->format('d/m/Y');
        }

        /**
         * closed_at accessor
         *
         * @param $value
         * @return string
         */
        public function getClosingDateAttribute($value)
        {
            $date = new Carbon($value);

            return $date->format('d-m-Y ') .' alle '. date('H:i:s');
        }

        /**
         * Retrieves all the media linked to this model
         *
         * @param string|null $mediaType
         * @param bool $showMediaFromNotes
         * @param array $ordering
         * @param bool $jobProof
         * @return $this
         */
        public function media(?string $mediaType = null, $showMediaFromNotes = false, array $ordering = [], bool $jobProof = false)
        {
            return $this->morphMany(Media::class, 'mediable')
                ->where(function($q) use ($mediaType, $showMediaFromNotes, $jobProof) {
                    if (null !== $mediaType) {
                        $q->where('media_type', $mediaType);
                    }

                    if (!$showMediaFromNotes) {
                        $q->whereNull('note_id');
                    }

                    $q->where('job_proof', '=', $jobProof);
                })
                ->orderBy((isset($ordering['type']) ? $ordering['type'] : 'ordering'), (isset($ordering['direction']) ? $ordering['direction'] : 'asc'));
        }
        
        /**
         * Retrieves all the media linked to this model
         *
         * @param string|null $user
         * @param int $buildingSite
         * @return $this
         */
        public function mediaToday(?string $user, int $buildingSite)
        {
            
            $mediaToday = DB::table('media')
                    ->where('media_type', 'image')
                    ->where('created_by', $user)
                    ->whereDate('created_at', '=', date('Y-m-d'))
                    ->where('directory', "building-site-".$buildingSite)
                    ->count();

            if ($mediaToday > 4)
                return true;
            else
                return false;

        }

        public function mediaTodayCount(?string $user, int $buildingSite)
        {
            
            $mediaToday = DB::table('media')
                    ->where('media_type', 'image')
                    ->where('created_by', $user)
                    ->whereDate('created_at', '=', date('Y-m-d'))
                    ->where('directory', "building-site-".$buildingSite)
                    ->count();

            return $mediaToday;

        }

        /**
         * Retrieves the machinery linked to this building site
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
         */
        public function machineries()
        {
            return $this->belongsToMany(Machinery::class);
        }

        /**
         * Retrieves some stats on building sites
         *
         * @return object
         */
        public function stats()
        {
            $query = $this->whereNotNull('created_at');


            return (object)[
                'total_sites' => $query->get()->count(),
                'open_sites' => $query->where('status', 'open')->get()->count()
            ];
        }

        /**
         * Returns the user that has been assigned as a manager to this building site
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function manager()
        {
            return $this->belongsTo(User::class, 'manager_id');
        }

        /**
         * Retrieves the employees that have access to this building site
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
         */
        public function employees()
        {
            return $this->belongsToMany(User::class, 'building_site_user');
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function notes()
        {
            return $this->hasMany(Note::class)
                ->orderBy('id', 'desc');
        }

        /**
         * Retrieves all the building site notes for this model
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function buildingSiteNotes()
        {
            return $this->hasMany(BuildingSiteNote::class);
        }

        /**
         * Retrieves all the reports for this building site
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function reports()
        {
            return $this->hasMany(Report::class);
        }

        /**
         * Retrieves all the customer reports for this building site
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function customerReports()
        {
            return $this->hasMany(CustomerReport::class);
        }

    }
