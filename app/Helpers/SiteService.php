<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request;
use Datetime;

class SiteService
{
    public static function dmy2mysql($input)
    {
        $output = null;
        if (preg_match('/^([1-9]|0[1-9]|[1-2][0-9]|3[0-1])\/([1-9]|0[1-9]|1[0-2])\/[0-9]{4}$/', $input)) { // is it in the correct format
            $date = explode('/', $input); // split the date string into its 3 parts of day, month and year.
            if (checkdate($date[1], $date[0], $date[2])) { // is it a valid date
                $timestamp = strtotime(str_replace('/', '.', $input)); // deal with uk/us date format issues by replacing / with .
                $output = date('Y-m-d', $timestamp); // convert to mySQL date format (YYYY-MM-DD)
            }
        }

        return $output;
    }

    public static function mysql2dmy($input, $format = 'long')
    {
        $european_date = null;
        $timestamp = strtotime($input);
        if ($timestamp != 0) {
            if ($format === 'short') {
                $european_date = date('d/m/y', $timestamp);
            } else {
                $european_date = date('d/m/Y', $timestamp);
            }
        }

        return $european_date;
    }

    public static function validateForm($rules, $contains_array_of_elements = false, $input = false)
    {

        /*=========================== UK Nat Ins Number ==================================>*/
        //extend the validator class to force input into AA 00 00 00 Z
        Validator::extend('nat_ins_no_uk_referral_exception', function ($attribute, $value, $parameters) {
            $valid = false;
            if(strlen($value) == 0)
            {
                $valid = true;
            }
            $value = preg_replace('/(\s+)|(-)/', '', $value);
            if (preg_match('/^[A-CEGHJ-PR-TW-Z]{1}[A-CEGHJ-NPR-TW-Z]{1}[0-9]{6}[A-D]{1}$/i', $value)) 
            {
                $valid = true;
            }
            // In case entered as referral, word 'referral' anywhere in the string
            if (strpos(strtoupper($value), 'REFERRAL') !== false) 
            {
                $valid = true;
            }

            return $valid;
        });

        /*=========================== Unique learner Number ==================================>*/
        //extend the validator class to force input into AA 00 00 00 Z
        Validator::extend('unique_learner_no', function ($attribute, $value, $parameters) {
            $valid = false;
            if(strlen($value) == 0)
            {
                $valid = true;
            }
            $value = preg_replace('/(\s+)|(-)/', '', $value);
            if (preg_match('/^[0-9]{10}$/', $value)) 
            {
                $valid = true;
            }

            return $valid;
        });

        /*=========================== UK Nat Ins Number ==================================>*/
        //extend the validator class to force input into AA 00 00 00 Z
        Validator::extend('nat_ins_no_uk', function ($attribute, $value, $parameters) {
            $valid = false;
            if(strlen($value) == 0)
            {
                $valid = true;
            }
            $value = preg_replace('/(\s+)|(-)/', '', $value);
            if (preg_match('/^[A-CEGHJ-PR-TW-Z]{1}[A-CEGHJ-NPR-TW-Z]{1}[0-9]{6}[A-D]{1}$/i', $value)) 
            {
                $valid = true;
            }

            return $valid;
        });

        /*=========================== Date format UK ===========================>*/
        //extend the validator class to force dates into the format d/y/yyyy
        Validator::extend('date_format_uk', function ($attribute, $value, $parameters) {
            $valid = false;

            if(strlen($value) == 0)
            {
                $valid = true;
            }
            if (preg_match('/^([1-9]|0[1-9]|[1-2][0-9]|3[0-1])\/([1-9]|0[1-9]|1[0-2])\/[0-9]{4}$/', $value)) 
            {
                $date = explode('/', $value); // split the date string into its 3 parts of day, month and year.
                if (checkdate($date[1], $date[0], $date[2])) 
                { // is it a valid date
                    $valid = true;
                }
            }
            return $valid;
        });

        /*=========================== Date of Birth UK format ===========================>*/
        //extend the validator class to check for date of births going in wrong
        Validator::extend('date_of_birth_uk_format', function ($attribute, $value, $parameters) {
            // Have to put this preg_match in as well or it will cause error when incorrect date format is put in.
            $valid = false;
            $valid_1 = false;
            if(strlen($value) == 0)
            {
                $valid = true;
            }
            if (preg_match('/^([1-9]|0[1-9]|[1-2][0-9]|3[0-1])\/([1-9]|0[1-9]|1[0-2])\/[0-9]{4}$/', $value)) 
            {
                $date = explode('/', $value); // split the date string into its 3 parts of day, month and year.
                if (checkdate($date[1], $date[0], $date[2])) { // is it a valid date
                    $valid_1 = true;
                }
            }
            if ($valid_1) {
                $date_of_birth = DateTime::createFromFormat('d/m/Y', $value);
                $date_cut_off = DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->modify('-15 years');

                if ($date_of_birth->format('Y-m-d') < $date_cut_off->format('Y-m-d')) {
                    $valid = true;
                }
            }

            return $valid;
        });


        /*=========================== Time ==================================>*/
        Validator::extend('time', function ($attribute, $value, $parameters) {
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
        });

        //set message for the validation rules
        $message_text = [
            'required' => 'required field',
            'time' => 'hh:mm 24hr format only',
            'alpha' => 'Letters only',
            'alpha_num' => 'Letters, numbers only',
            'alpha_number' => 'Letters, numbers only',
            'alpha_hyphen' => 'Letters, hyphens, spaces only',
            'alpha_num_plus' => 'Invalid characters',
            'alpha_name' => 'Letters, hyphens, and apostrophes only',
            'integer' => 'Number only',
            'nat_ins_no_uk' => 'Invalid NINO Format',
            'nat_ins_no_uk_referral_exception' => 'Invalid NINO Format',
            'unique_learner_no' => '10 Digit Number Only',
            'date_of_birth_uk_format' => 'out of accepted range',
            'numeric' =>'number only',
        ];


     

        if (! $contains_array_of_elements) {
            $validator = Validator::make(request()->all(), $rules, $message_text);
        } else {
            $validator = Validator::make($input, $rules, $message_text);
        }

        $messages = [];
        if ($validator->fails()) {
            $errors = $validator->messages();
            return $errors;
        }

        return $messages;
    }

    public static function setNullIfEmptyOrFalse($input)
    {
        $output = null;
        if (! $input or $input === 'false') {
        } elseif (! empty($input)) {
            $output = $input;
        }

        return $output;
    }

    public static function sqlDump($query, $bind)
    {
        echo 'SQL:<br>'.$query.'<br><br>Bind:';
        dd($bind);
    }

    public static function randString($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $size_chars = strlen($chars);
        $size_nums = strlen($numbers);
        $str = false;
        for ($i = 0; $i < $length - 1; $i++) {
            $str .= $chars[mt_rand(0, $size_chars - 1)];
        }
        $str .= $numbers[mt_rand(0, $size_nums - 1)];
        $str = str_shuffle($str);

        return $str;
    }

    public static function listsToOptgroup($lists, $placeholder = false)
    {
        $optgroup = false;
        $options = [];
        foreach ($lists as $key => $value) {
            $first_char = ucfirst($value[0]);
            if ($first_char !== $optgroup) {
                $optgroup = $first_char;
                $options[$optgroup] = [$key=>$value];
            } else {
                $options[$optgroup] = $options[$optgroup] + [$key=>$value];
            }
        }
        //		$options = array(null =>array( $placeholder)) + $options;
        if ($placeholder) {
            $options = [null=>$placeholder] + $options;
        }

        return $options;
    }

    public static function preAssignMarkerToSelectKey($lists, $marker)
    {
        $array = [];
        foreach ($lists as $key => $value) {
            $array[$marker.$key] = $value;
        }

        return $array;
    }

    public static function diffInMonths($start, $end)
    {
        $diffYears = $end->format('Y') - $start->format('Y');
        $diffMonths = $end->format('m') - $start->format('m');

        if ($diffMonths < 0) {
            $diffMonths = 12 + $diffMonths;
            if ($diffYears > 0) {
                $diffYears -= 1;
            }
        }
        $total_diff = $diffMonths + ($diffYears * 12);

        return $total_diff;
    }

    public static function dateDiff($date1, $date2, $type)
    {
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $interval = $datetime1->diff($datetime2);

        //full days:
        if ($type == 'day') {
            return $interval->days;
        }
        //full weeks:
        elseif ($type == 'week') {
            return floor($interval->days / 7);
        }
        //full months:
        elseif ($type == 'month') {
            return ($interval->y * 12) + $interval->m;
        }
        //full years:
        elseif ($type == 'year') {
            return $interval->y;
        } else {
            return null;
        }
    }

    public static function checkIncludeOccurance($row)
    {
        $include = false;
        switch ((int) $row->recurrance_type) :
                case 0:
                   return true;
        break;
        case 1:
                   return true;
        // if ((int)$row->week_diff === 0 || (int)$row->week_diff % (int)$row->recurrance_interval === 0) {$include = true;}
        break;
        case 2:
                    $start_date = DateTime::createFromFormat('Y-m-d', $row->session_start_date);
        $session_date = DateTime::createFromFormat('Y-m-d', $row->session_date);
        $recurrance_days = [1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat', 7 => 'sun'];
        $occurance_date = clone $session_date;
        $occurance_date->modify($row->recurrance_monthly_interval.' '.$recurrance_days[$row->sessDay].' of '.$session_date->format('M'));
        if ($occurance_date->format('Y-m-d') !== $session_date->format('Y-m-d')) {
            return false;
        }
        $diff_months = self::diffInMonths($start_date, $occurance_date);
        if ((int) $diff_months % (int) $row->recurrance_interval === 0) {
            $include = true;
        }
        break;
        endswitch;

        // dd($indlude);

        return $include;
    }

    public static function checkStaffServicePermissions($staff, $service_id)
    {
        $services = false;

        if (is_numeric($service_id)) {
            // Has the logged in staff member permissions to view this service?
            if (! in_array($service_id, $staff->services)) {
                die('<h3 style="color:red;">Logged in staff member does not have permissions to view this service!</h3>');
            }
            $services = [$service_id];
        } else {
            $services = $staff->services;
        }

        return $services;
    }

    public static function checkBrowserServerServiceUserMismatch($service_user_id_browser, $service_user_server)
    {
        $match = (int) $service_user_id_browser === (int) $service_user_server->id ? true : false;

        if (! $match) {
            Session::flash('message', '*Warning: Name Mismatch! Please ensure the participant name below is whom you wish to update and then try again </br>This is caused by browser page caching (please use application navigation buttons instead of browser back button) or opening multiple tabs on different names');
            Session::flash('message_class', 'alert-warning');

            DB::table('error_specific_log')
                    ->insert([
                        'error_details' => 'Browser/Server mismatch alert given',
                        'date_occured' => date('Y-m-d H:i:s'),
                        'staff_id' => Session::get('staff')->id,
                        ]);
        }

        return $match;
    }

    public static function validateTime24hrClock($time)
    {

            // does not work
        // if(!preg_match("/(1[012]|0[0-9]):([0-5][0-9])/", $time)){
        // 	return false;
        // }else{
        // 	return $time;
        // }

        return $time;
    }

    public static function dd($output)
    {
        dd('<pre>'.print_r($output, 1));
    }

    public static function correctNINOFormat($nino)
    {
        // In case entered as referral
        if (strpos(strtoupper($nino), 'ENTERED') !== false) {
            return $nino;
        }

        // Uppercase all / remove all spaces / put spaces every 2 letters back in / take trailing spaces off
        $nino = trim(chunk_split(preg_replace('/(\s+)|(-)/', '', strtoupper($nino)), 2, ' '));

        return $nino;
    }

    public static function trim($array)
    {
        foreach ($array as $key1 => $value) {
            if (is_array($value)) {
                foreach ($value as $key2 => $val) {
                    $val = self::clean_problem_chars($val);
                    $array[$key1][$key2] = trim($val);
                }
            } else {
                $value = self::clean_problem_chars($value);
                $array[$key1] = trim($value);
            }
        }

        return $array;
    }

    private static function clean_problem_chars($string)
    {
        $needles = [
                '>',
                '<',
            ];

        $string = strip_tags($string);
        $string = preg_replace('~\xc2\xa0~', ' ', $string); // gets rid of problem character Â
        $string = str_replace($needles, ' ', $string);

        return $string;
    }
}
