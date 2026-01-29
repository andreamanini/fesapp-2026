<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Machinery extends Model
    {
        protected $guarded = ['id'];

        /**
         * Retrieves all the media linked to this model
         *
         * @param string|null $mediaType
         * @return Model|null|object|static
         */
        public function media(?string $mediaType = null)
        {
            return $this->morphMany(Media::class, 'mediable')
                ->where(function($q) use ($mediaType) {
                    if (null !== $mediaType) {
                        $q->where('media_type', $mediaType);
                    }
                })
                ->orderBy('ordering', 'asc');
        }

        /**
         * Retrieves the first media linked to this record
         *
         * @return Model|null|object|static
         */
        public function mainImage()
        {
            return $this->media()->first();
        }

        /**
         * Retrieves the building sites linked to this machinery
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
         */
        public function buildingSites()
        {
            return $this->belongsToMany(BuildingSite::class);
        }
    }
