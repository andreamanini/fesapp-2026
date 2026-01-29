<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class BuildingSiteNote extends Model
    {
        protected $guarded = ['id'];

        /**
         * note_date field accessor function
         *
         * @param $value
         * @return string
         */
        public function getNoteDateAttribute($value)
        {
            if (!empty($value)) {
                $array = explode('-', $value);

                return $array[2] . '/' . $array[1] . '/' . $array[0];
            }
        }
    }
