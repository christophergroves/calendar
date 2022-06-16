<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;


class Time implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
          /*=========================== Time ==================================>*/
          
            $valid = false;
            if(strlen($value) == 0)
            {
                $valid = true;
            }
            if (preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $value)) 
            {
                $valid = true;
            }

            return $valid;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'hh:mm 24hr format only';
    }
}
