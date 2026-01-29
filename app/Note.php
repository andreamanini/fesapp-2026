<?php

    namespace App;

    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class Note extends Model
    {
        use SoftDeletes;

        protected $guarded = ['id'];

        /**
         * Retrieves the building site this note has been connected to
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function buildingSite()
        {
            return $this->belongsTo(BuildingSite::class);
        }

        /**
         * Retrieves the user record that created the note
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function employee()
        {
            return $this->belongsTo(User::class, 'user_id');
        }

        /**
         * Retrieves the media records that this note has associated with
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function media()
        {
            return $this->hasMany(Media::class);
        }

        /**
         * created_at accessor
         *
         * @param $value
         * @return string
         */
        public function getCreatedAtAttribute($value)
        {
            $date = new Carbon($value);

            return $date->format('d-m-Y H:i:s');
        }
    }
