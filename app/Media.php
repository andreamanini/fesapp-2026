<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Media extends Model
    {
        protected $guarded = ['id'];

        public $thumbNames = [
            'thumb_',
        ];

        /**
         * Notes attribute accessor
         *
         * @param $value
         * @return array|mixed
         */
        public function getNotesAttribute($value)
        {
            return (null !== $value ? json_decode($value) : []);
        }

        /**
         * Function used to retrieve the full path
         *
         * @param null|string $imgDimension
         * @return string
         */
        public function getFullPath(?string $imgDimension = null)
        {
            $imgUrl = (isset($this->directory) ? $this->directory . '/' : '') .
                (null !== $imgDimension ? $imgDimension . '_' : '') .
                (isset($this->media_name) ? $this->media_name : '') .
                (isset($this->extension) ? '.' . $this->extension : '');


            return (isset($this->origin_url) ? $this->origin_url . '/' . $imgUrl : asset("media/{$imgUrl}"));
        }
    }
