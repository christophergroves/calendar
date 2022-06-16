<?php 

namespace App\Services;

use DateTime;
use stdClass;
use App\Models\User;
use App\Models\Session;
use App\Models\Activity;
use App\Helpers\SiteService;
use App\Models\SessionAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Facade\FlareClient\Http\Response;

require_once app_path().'/Includes/constants/sql_constants.php';

Class CalendarService
{

public static function getMonth($user)
    {
        $events = [];

        if ($user) 
        {
            $week_beginning = DateTime::createFromFormat('Y-m-d', $_GET['start']);
            $week_ending = clone $week_beginning;
            $week_ending->modify('+6 Day');

            for ($i = 0; $i < 6; $i++) 
            {
                $allrows = self::getEventsQuery($week_beginning, $week_ending, $user);

                if ($allrows) 
                {
                    foreach ($allrows as $row) 
                    {
                        if (!self::checkIncludeOccurance($row)) { continue;}

                        switch ($row->category): 
                            case 'Social': $background_color = '#3A87AD';
                            break;
                            case 'Training': $background_color = '#3A87AD;';
                            break;
                            case 'Practical': $background_color = '#9B5115';
                            break;
                            case 'WorkExp': $background_color = '#699E35';
                            break;
                            case 'ProgPlanning': $background_color = '#8092A3';
                            break;
                            default: $background_color = '#3A87AD';
                        endswitch;
                        
                        $event = [];

                        foreach($row as $key => $value)
                        {
                            $event[$key] = strip_tags($value);
                        }

                        if ($row->start_time) {$event['start'] .= 'T'.strip_tags($row->start_time);}
                        $event['textColor'] = $row->attendance ? '#FFD20F' : 'white';
                        $event['backgroundColor'] = $background_color;
                        $event['borderColor'] = $background_color;
                        $event['week_beginning'] = $week_beginning->format('Y-m-d');
                        $event['week_ending'] = $week_ending->format('Y-m-d');
                        $events[] = $event;
                    }
                }
                $week_beginning->modify('+1 Week');
                $week_ending->modify('+1 Week');
            }
        }

        $events = ['events'=>$events, 'user'=> new UserResource($user)];
        return $events;
    }



    private static function checkIncludeOccurance($row)
    {
        $include = false;
        switch ((int) $row->recurrance_type) :
        case 0:
            return true;
        break;
        case 1:
            return true;
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
        return $include;
    }




    private static function getEventsQuery($week_beginning, $week_ending, $user)
    {
        $bind = [
            ':week_beginning' => $week_beginning->format('Y-m-d'),
            ':week_ending' => $week_ending->format('Y-m-d'),
            ':user_id' => $user->id,
        ];


        $query = "SELECT
        activities.user_id,
        users.name, 
        sessions.id AS session_id,
        sessions.parent_id,
        sessions.session_day AS session_day,
        sessions.activity_id,
        sessions.recurrance_type,
        sessions.recurrance_interval,
        sessions.recurrance_number,
        sessions.recurrance_monthly_interval,
        activity_types.category,
   
        CONCAT(activity_types.name,' @ ',venues.name,' - ',activities.description) AS title,

        DATE_ADD(:week_beginning, INTERVAL sessions.session_day - 1 DAY) as start,

        DATE_FORMAT(sessions.start_date,'%d/%m/%Y') AS session_start_date,
        DATE_FORMAT(sessions.finish_date,'%d/%m/%Y') AS session_finish_date,

        DATE_FORMAT(start_time, '%H:%i') as start_time,
        DATE_FORMAT(finish_time, '%H:%i') as finish_time,

        session_attendances.absence as attendance,
        session_attendances.attendance_notes,
        session_attendances.absense_date,
        sessions.hours,
        TSubChild.parent_id_child,
        session_attendances.session_deleted

        FROM
        sessions
        INNER JOIN activities ON activities.id = sessions.activity_id

        INNER JOIN activity_types ON activity_types.id = activities.activity_type_id

        INNER JOIN venues ON venues.id = activities.venue_id
        INNER JOIN users ON users.id = activities.user_id
        LEFT JOIN session_attendances ON session_attendances.session_id = sessions.id AND session_attendances.absense_date = DATE_ADD(:week_beginning,INTERVAL sessions.session_day - 1 DAY)


        LEFT JOIN 
        (SELECT
        sessions.parent_id AS parent_id_child,
        sessions.start_date,
        sessions.finish_date
        FROM 
        sessions)
        AS TSubChild ON TSubChild.parent_id_child = sessions.id AND 
        TSubChild.start_date = DATE_ADD(:week_beginning,INTERVAL sessions.session_day - 1 DAY) AND
        TSubChild.finish_date = DATE_ADD(:week_beginning,INTERVAL sessions.session_day - 1 DAY)
        

        WHERE

        activities.user_id = :user_id AND
    
        sessions.start_date <= :week_ending AND

        (sessions.finish_date IS NULL OR 
        sessions.finish_date = 0 OR 
        sessions.finish_date >=  DATE_ADD(:week_beginning,INTERVAL sessions.session_day - 1 DAY))

        AND
        (session_attendances.id IS NULL OR session_attendances.session_deleted IS NULL OR session_attendances.session_deleted = 0) 

        AND 
        (sessions.recurrance_type = 0 || sessions.recurrance_type = 2 ||
        (DATEDIFF(sessions.start_date,DATE_ADD(:week_beginning, INTERVAL sessions.session_day - 1 DAY))/7) % sessions.recurrance_interval = '0')

        GROUP BY sessions.id
        ORDER BY session_day ASC,
        sessions.start_time ASC";

        $allrows = DB::select($query, $bind);
        return $allrows;
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









    public static function getActivitiesEditList($user)
    {

        $activities = Activity::select(DB::raw('CONCAT(activity_types.name," @ ",venues.name," - ",activities.description) AS name'), 'activities.id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->join('venues', 'venues.id', '=', 'activities.venue_id')
            ->where('activities.user_id', $user->id)
            ->orderBy('activity_types.list_position')
            ->pluck('name', 'id')->all();

        $activities = [''=>'Please select an activity ...'] + ['Activities List For '.$user->name.':' => $activities];

        return $activities;
    }


    public static function saveInstance($request, $user)
    {

        dd('save instance');
        $day_date = false;
        $start_date_monthly = false;

        // get session details filtered by service user id
        $session_orig = Session::select('sessions.*')
            ->where('sessions.id', $request['sessionId'])
            ->whereUserId($user->id)
            ->join('activities', 'activities.id', '=', 'sessions.activity_id')
            ->first();

        $session = Session::whereid($session_orig->id)->first();

        // $session = clone $session_orig;

        $input_orig['recurrance_number'] = $session_orig->recurrance_number;
        $input_orig['recurrance_type'] = $session_orig->recurrance_type;
        $input_orig['recurrance_interval'] = $session_orig->recurrance_interval;
        $input_orig['recurrance_monthly_interval'] = $session_orig->recurrance_monthly_interval;
        $input_orig['recurrance_day_single'] = $session_orig->session_day;

        // get session date from parameter (date clicked) and session_date from form element
        // $session_date_form = DateTime::createFromFormat('d/m/Y', $request['session_date']);
        $session_date_param = DateTime::createFromFormat('Y-m-d', $request['sessionDate']);
        $session_date_form = array_key_exists('session_date', $request) ? DateTime::createFromFormat('d/m/Y', $request['sessionDate']) : $session_date_param;
        $start_date_form = array_key_exists('start_date', $request) ? DateTime::createFromFormat('d/m/Y', $request['start_date']) : $session_date_param;

        switch ((int) $session->recurrance_type) :
            case 0:
            break;
        // weekly repeat
        case 1:
                // if no days checkboxes are checked then get day from session date
            if (! array_key_exists('recurrance_day', $request)) 
            {
                $request['recurrance_day'][0] = DateTime::createFromFormat('d/m/Y', $request['sessionDate'])->format('N');
            }
            // is there more than one day checked?
            if (count($request['recurrance_day']) > 1) 
            {
                echo 'Error! Only one day allowed to be checked!';
                return false;
            }
        // get the day number
        foreach ($request['recurrance_day'] as $day) 
        {
            if ($day) 
            {
                $day_no = $day;
            }
        }
        // get actual date of checked day
        $diff = $day_no - $session_date_param->format('N');
        $day_date = clone $session_date_param;
        $day_date->modify($diff.' Day');
        break;
        // monthly repeat
        case 2:
        // correct to get actual start date (i.e. first monday of August)
        $recurrance_days = [1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat', 7 => 'sun'];
        $start_date_monthly = clone $session_date_param;
        $start_date_monthly->modify($request['recurrance_monthly_interval'].' '.$recurrance_days[$request['recurrance_day_single']].' of '.$start_date_monthly->format('M'));
        break;
        endswitch;

        // switch to find out which date to set for the start date
        switch (true) :
            case $session_date_param->format('Y-m-d') !== $session_date_form->format('Y-m-d'):  // if session date on form has changed
                // take the date from the session date on form
                $actual_start_date = $session_date_form;
        break;
        case $session->start_date !== $start_date_form->format('Y-m-d'):  // if start date on form has changed
                // take the date from the start date on form
                $actual_start_date = $start_date_form;
        break;
        case $day_date && $day_date->format('Y-m-d') !== $start_date_form->format('Y-m-d'): // if day checkbox has changed
                // take the date from the day checkbox
                $actual_start_date = $day_date;
        break;
        case $start_date_monthly && $start_date_monthly->format('Y-m-d') !== $session_date_param->format('Y-m-d'):
                $actual_start_date = $start_date_monthly;
        break;
        default:
                // else leave date as it was
                $actual_start_date = $session_date_param;
        endswitch;





        // set 'session_deleted' flag on attendance table to flag this session on this session date is to be ommited but only if it is a recurring session.
        if (! $session->parent_id) {
            $attendance = SessionAttendance::whereSession_id($session->id)->whereAbsence_date($request['sessionDate'])->first();
            if (! $attendance) {
                $attendance = new SessionAttendance;
            }
            $attendance->session_id = $session->id;
            $attendance->absence_date = $session_date_param->format('Y-m-d');
            $attendance->updated_by = $user->id;
            $attendance->session_deleted = 1;
            $attendance->save();
        }

        // if session is child then get parent and session model
        if ($session->parent_id) {
            $session_parent = DB::table('sessions')->whereId($session->parent_id)->first();
        } else {
            $date_correction = self::correctStartFinishDateCleanUpDeletes($session_orig, $input_orig, $session_date_param);

            if ($date_correction) {
                if ($date_correction['start_date']) {
                    $session->start_date = $date_correction['start_date']->format('Y-m-d');

                    if ($session_orig->finish_date === $date_correction['start_date']->format('Y-m-d')) {
                        $session->recurrance_type = 0;
                    }
                } elseif ($date_correction['finish_date']) {
                    $session->finish_date = $date_correction['finish_date']->format('Y-m-d');

                    if ($session_orig->start_date === $date_correction['finish_date']->format('Y-m-d')) {
                        $session->recurrance_type = 0;
                    }
                }

                $session->recurrance_number = null;
                $session->ends_on = 2;
                $session->updated_by = $staff->id;
                $session->save();
            }
            $session_parent = $session;
            $session = new SessionModel;
        }

        if (! $session) {
            $session = new SessionModel;
        }
        $session->fill($request); //mass fill
        if ($session_parent) {
            $session->parent_id = $session_parent->id;
        }
        $session->recurrance_type = 0;
        $session->recurrance_number = null;
        $session->recurrance_interval = null;
        $session->recurrance_monthly_interval = null;
        $session->session_day = $actual_start_date->format('N');
        $session->start_date = $actual_start_date->format('Y-m-d');
        $session->finish_date = $actual_start_date->format('Y-m-d');
        $session->start_time = SiteService::setNullIfEmptyOrFalse(SiteService::validateTime24hrClock($request['start_time']));
        $session->finish_time = SiteService::setNullIfEmptyOrFalse(SiteService::validateTime24hrClock($request['finish_time']));
        $session->hours = $request['hours'] ? $request['hours'] : 4;
        $session->updated_by = $staff->id;
        $session->save();


        // if a new session has been created then copy the attendance over to this new session
        if ($session_orig && (int) $session_orig->id !== (int) $session->id) {
            self::transferSessionAttendanceToNewSession($session, $session_orig, $session_date_param, $staff);
        }
    }



    public static function saveNoRepeats($request, $user, $changed_to_instance)
    {

        if ($request['sessionId']) {
            $session_orig = Session::select('sessions.id')
            ->where('sessions.id', $request['sessionId'])
            ->whereUser_id($user->id)
            ->join('activities', 'activities.id', '=', 'sessions.activity_id')
            ->first();

            $session_orig = Session::whereId($session_orig->id)->first();

            $session_orig_start_date = DateTime::createFromFormat('Y-m-d', $session_orig->start_date);
            $session_date_param = DateTime::createFromFormat('Y-m-d', $request['sessionDate']);

            // If changed to an instance from weekly/monthly then check if it is the first one of the series
            if ($changed_to_instance === '1' && ($session_orig_start_date->format('Y-m-d') !== $session_date_param->format('Y-m-d'))) 
            {
                $request['recurrance_type'] === $session_orig->recurrance_type;
                $session_orig = self::finishOffExistingRecurringSet($session_date_param, $session_orig, $request); // finish off old
                $session_orig->save();
                $session = $session_orig->replicate(); // create a new one based on the finished off one
            } 
            else 
            {
                $session = $session_orig;
            }
        } 
        else 
        {
            $session = new Session;
        }

        $session->fill($request); //mass fill
        $session->recurrance_number = null;
        $session->ends_on = 0;
        $session->recurrance_interval = null;
        $session->recurrance_monthly_interval = null;
        $session->session_day = DateTime::createFromFormat('d/m/Y', $request['session_date'])->format('N');
        $session->start_date = SiteService::dmy2mysql($request['session_date']);
        $session->finish_date = SiteService::dmy2mysql($request['session_date']);
        $session->start_time = SiteService::setNullIfEmptyOrFalse(SiteService::validateTime24hrClock($request['start_time']));
        $session->finish_time = SiteService::setNullIfEmptyOrFalse(SiteService::validateTime24hrClock($request['finish_time']));
        $session->hours = $request['hours'] !== '' ? $request['hours'] : 4;
        $session->updated_by = $user->id;
        $session->save();


        return response('success',200);
    }


    public static function saveRepeatWeekly($request, $user)
    {
        $session_orig = false;
        $session_date_param = DateTime::createFromFormat('Y-m-d', $request['sessionDate']);
        $session_date_form = DateTime::createFromFormat('d/m/Y', $request['session_date']);
        $start_date_form = DateTime::createFromFormat('d/m/Y', $request['start_date']);

        // if session date on form has changed then use session_date on form to get day number and set '$request['recurrance_day'][0]' to only one value (not multiple days) * DateTime::format('N') gives you the day number
        if ($session_date_param->format('Y-m-d') !== $session_date_form->format('Y-m-d')) 
        {
            $request['recurrance_day'][0] = $session_date_form->format('N');
        // else if no day checkboxes are selected then set only day number to start_date on form
        } 
        elseif (! array_key_exists('recurrance_day', $request)) 
        {
            // $request['recurrance_day'][0] = DateTime::createFromFormat('d/m/Y', $request['start_date'])->format('N');
            $request['recurrance_day'][0] = $start_date_form->format('N');
        }

        // if this is a new session record to be added
        if ($request['sessionId'])
        {
            $session_orig = DB::table('sessions')->select('sessions.*')
                    ->where('sessions.id', $request['sessionId'])
                    ->whereUser_id($user->id)
                    ->join('activities', 'activities.id', '=', 'sessions.activity_id')
                    ->first();

            $orig_start_date = DateTime::createFromFormat('Y-m-d', $session_orig->start_date);
        }

        foreach ($request['recurrance_day'] as $day_no) 
        {
            if ($request['sessionId']) 
            {

                // get session model
                $session = Session::whereId($session_orig->id)->first();

                // count number of days that are checked in checkbox
                $count = count($request['recurrance_day']);

                switch (true) :
                    // is the original session day different from the checked day currently in the loop and is more that one day checked then create new session model
                    case (int) $session->session_day !== (int) $day_no && $count > 1:
                        $session = new Session;
                break;
                // if edit-now-on and session date that is clicked on calendar is different from original session start date then finnish off existing recurrance set and create new model
                case $request['action'] === 'edit-now-on' && $orig_start_date->format('Y-m-d') !== $session_date_param->format('Y-m-d'):

                $session = self::finishOffExistingRecurringSet($session_date_param, $session, $request);
                $new_session_finish_date = DateTime::createFromFormat('Y-m-d', $session->finish_date);

                $date_correction = self::correctStartFinishDateCleanUpDeletes($session, $request, $new_session_finish_date);

                if ($date_correction && $date_correction['finish_date']) 
                {
                    $session->finish_date = $date_correction['finish_date']->format('Y-m-d');

                    if ($session_orig->start_date === $date_correction['finish_date']->format('Y-m-d')) 
                    {
                        $session->recurrance_type = 0;
                    }
                }

                $session->recurrance_number = null;
                $session->ends_on = 2;
                $session->updated_by = $staff->id;
                $session->save();

                $session = new Session;
                break;
                endswitch;
            } 
            else 
            {
                $session = new Session;
            }

            // set start_date (before correction) to either the session date that is clicked on calendar or the form start date field
            if ($request['action'] === 'edit-now-on') 
            {   // if the start date on form has changed and is after the original start date then set this as start date (as this is edit-now-on)
                if ($start_date_form->format('Y-m-d') > $orig_start_date->format('Y-m-d')) 
                {
                    $start_date = DateTime::createFromFormat('d/m/Y', $request['start_date']);
                } 
                else 
                {
                    $start_date = DateTime::createFromFormat('d/m/Y', $request['session_date']);
                }
            } 
            else 
            {
                $start_date = DateTime::createFromFormat('d/m/Y', $request['start_date']);
            }

            // correct the start date via the day number checkbox
            $actual_start_date = self::correctInputStartDateRepeatWeekly($start_date, $day_no);


            // switch for form 'ends on' 0:Never,1:Ends on occurances, 2:Ends on date
            switch ($request['ends_on']) :
                case 'ends_never':
                    $session->finish_date = null;
            $session->recurrance_number = null;
            $session->ends_on = 0;
            break;
            case 'ends_on_occurances':
                    if ($request['recurrance_number']) {
                        $session->finish_date = self::calculateFinishDateFromOccurances($actual_start_date, $request);
                        $session->recurrance_number = null;
                        $session->ends_on = 2;
                    } else {
                        $session->finish_date = null;
                        $session->recurrance_number = null;
                        $session->ends_on = 0;
                    }
            break;
            case 'ends_on_date':
                    $actual_finish_date = null;
            $request['finish_date'] = SiteService::setNullIfEmptyOrFalse($request['finish_date']);
            if ($request['finish_date']) {
                $actual_finish_date = DateTime::createFromFormat('d/m/Y', $request['finish_date']);
                $actual_finish_date = self::correctInputFinishDateRepeatWeekly($actual_finish_date, $day_no, $request);
            }
            $session->finish_date = $actual_finish_date;
            $session->recurrance_number = null;
            $session->ends_on = $actual_finish_date ? 2 : 0;
            break;
            endswitch;

            $session->activity_id = $request['activity_id'];

            $session->start_date = $actual_start_date->format('Y-m-d');

            $session->recurrance_type = $request['recurrance_type'];
            $session->recurrance_interval = $request['recurrance_interval'];

            $session->recurrance_monthly_interval = null;
            $session->session_day = $day_no;
            $session->start_time = SiteService::setNullIfEmptyOrFalse(SiteService::validateTime24hrClock($request['start_time']));
            $session->finish_time = SiteService::setNullIfEmptyOrFalse(SiteService::validateTime24hrClock($request['finish_time']));
            $session->hours = $request['hours'] !== '' ? $request['hours'] : 4;
            $session->updated_by = $user->id;
            $session->save();

            // if a new session has been created then copy the attendance over to this new session
            if ($session_orig && (int) $session_orig->id !== (int) $session->id) {
                self::transferSessionAttendanceToNewSession($session, $session_orig, $session_date_param, $staff);
            }
        }
    }



    private static function correctStartFinishDateCleanUpDeletes($session_orig, $input, $session_date_drag_start, $session_date_at_drop = false, $remove_action = false)
    {
        $possible_delete_date = false;
        $date_correction = ['start_date'=>false, 'finish_date'=>false];
        $possible_deletes = [];
        $deletes_found = [];
        $recurrance_interval = $input['recurrance_interval'];
        $session_orig_start_date = $session_orig->start_date;
        $recurrance_days = [1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat', 7 => 'sun'];

        // $possible_delete_date = $session_orig->finish_date ?  DateTime::createFromFormat('Y-m-d', $session_orig->finish_date) : false;

        // This is to fix the issue where if there is no finish date and the first in the series is deleted the new start date does not correct. Also if the last in the series is deleted then the start date does not correct.
        if (
            $remove_action === 'delete-instance' &&
            ((int) $session_orig->recurrance_type === 1 || (int) $session_orig->recurrance_type === 2) &&
            $session_date_drag_start->format('Y-m-d') === $session_orig->start_date) 
        {
            $term = (int) $session_orig->recurrance_type === 1 ? 'WEEK' : 'MONTH';
            $possible_delete_date = $session_orig->finish_date ? DateTime::createFromFormat('Y-m-d', $session_orig->finish_date) : DateTime::createFromFormat('Y-m-d', $session_orig->start_date)->modify('+'.$recurrance_interval * 104 .' '.$term);
        } 
        elseif (
            $remove_action === 'delete-instance' &&
            ((int) $session_orig->recurrance_type === 1 || (int) $session_orig->recurrance_type === 2) &&
            $session_date_drag_start->format('Y-m-d') === $session_orig->finish_date) 
        {
            $possible_delete_date = $session_orig->finish_date ? DateTime::createFromFormat('Y-m-d', $session_orig->finish_date) : false;
        }

        if (! $possible_delete_date) 
        {
            return false;
        }

        if ((int) $session_orig->recurrance_type === 1) 
        {

            // step through possible delete dates and store them in $possible_deletes
            for ($i = 1; $i <= 150; $i++) 
            {
                $possible_deletes[] = $possible_delete_date->format('Y-m-d');
                $possible_delete_date->modify('-'.$recurrance_interval.' Week');

                // if we overshoot the start date then break
                if ($possible_delete_date->format('Y-m-d') < $session_orig_start_date) 
                {
                    break;
                }
            }
        } 
        elseif ((int) $session_orig->recurrance_type === 2) 
        {

            // step through possible delete dates and store them in $possible_deletes
            for ($i = 1; $i <= 150; $i++) 
            {
                $possible_deletes[] = $possible_delete_date->format('Y-m-d');
                $possible_delete_date->modify('first day of this month');
                $possible_delete_date->modify('- '.$input['recurrance_interval'].' Month');
                $possible_delete_date->modify($input['recurrance_monthly_interval'].' '.$recurrance_days[$input['recurrance_day_single']].' of '.$possible_delete_date->format('M'));

                // if we overshoot the start date then break
                if ($possible_delete_date->format('Y-m-d') < $session_orig_start_date) 
                {
                    break;
                }
            }
        }

        // look for session dates that have been flagged as deleted
        $deletes = DB::table('attendance')->select('id', 'absence_date')
            ->where('session_id', $session_orig->id)
            ->where('session_deleted', 1)
            ->whereIn('absence_date', $possible_deletes)
            ->orderBy('absence_date', 'DESC')
            ->get();

        // $deletes_found = array();
        foreach ($deletes as $delete) 
        {
            $deletes_found[] = $delete->absence_date;
        }

        if (count($deletes_found) < 1) 
        {
            return $date_correction;
        }

        if (count($possible_deletes) === count($deletes_found)) 
        {
            $session = SessionModel::whereId($session_orig->id)->first();
            $session->delete();

            return $date_correction;
        }

        /*================= GOING BACK FROM THE FINISH DATE TO THE START DATE CORRECTING FINISH DATE AND DELETING ATTENDANCE SESSION DELETES ===============>*/

        // if you are deleting the last event in a line of recurring events then go back until you find an active event occurance that has not been deleted
        if ($session_orig->finish_date === $session_date_drag_start->format('Y-m-d')) 
        {
            switch ($session_orig->recurrance_type) :
                    case 1: // Weelky

                         $finish_date_correction = DateTime::createFromFormat('Y-m-d', $session_orig->finish_date);

            // if the date at drop is one of the deleted dates then delete the attendance record on this date
            if ($session_date_at_drop && in_array($session_date_at_drop->format('Y-m-d'), $deletes_found)) 
            {
                DB::table('attendance')->whereSession_id($session_orig->id)->whereAbsence_date($session_date_at_drop->format('Y-m-d'))->delete();
            }

            // keep taking the finish date back while you still find events that have the deleted flag set
            for ($i = 1; $i <= 150; $i++) 
            {
                if (in_array($finish_date_correction->format('Y-m-d'), $deletes_found)) {
                    // keep taking the finish date back while you still find events that have the deleted flag set (weekly events)
                    DB::table('attendance')->whereSession_id($session_orig->id)->whereAbsence_date($finish_date_correction->format('Y-m-d'))->delete();
                    $finish_date_correction->modify('-'.$recurrance_interval.' Week');
                } 
                else 
                {
                break;
                }
            }
            break;

            case 2: // Monthly

                        $finish_date_correction = DateTime::createFromFormat('Y-m-d', $session_orig->finish_date)->modify('first day of this month');
            $finish_date_correction->modify($input['recurrance_monthly_interval'].' '.$recurrance_days[$input['recurrance_day_single']].' of '.$finish_date_correction->format('M'));

            // if the date at drop is one of the deleted dates then delete the attendance record on this date
            if ($session_date_at_drop && in_array($session_date_at_drop->format('Y-m-d'), $deletes_found)) {
                DB::table('attendance')->whereSession_id($session_orig->id)->whereAbsence_date($session_date_at_drop->format('Y-m-d'))->delete();
            }

            // keep taking the finish date back while you still find events that have the deleted flag set and deleting the appropriate attendance row (monthly events)
            for ($i = 1; $i <= 150; $i++) 
            {
                if (in_array($finish_date_correction->format('Y-m-d'), $deletes_found)) {
                    //delete the attendance row which matches this the date you have stepped back to at this point
                    DB::table('attendance')->whereSession_id($session->id)->whereAbsence_date($finish_date_correction->format('Y-m-d'))->delete();
                    $finish_date_correction->modify('first day of this month')->modify('-'.$recurrance_interval.' month');
                    $finish_date_correction->modify($input['recurrance_monthly_interval'].' '.$recurrance_days[$input['recurrance_day_single']].' of '.$finish_date_correction->format('M'));
                } 
                else 
                {
                break;
                }
            }
            break;
            default:
                endswitch;

            $date_correction['finish_date'] = $finish_date_correction;
        /*====================================================================================================================================================>*/

        /*================= GOING FORWARDS FROM THE START DATE TO THE FINISH DATE CORRECTING START DATE AND DELETING ATTENDANCE SESSION DELETES ===============>*/

        // if you are deleting the first event in a line of recurring events then go forwards until you find an active event occurance that has not been deleted
        } 
        elseif ($session_orig->start_date === $session_date_drag_start->format('Y-m-d')) 
        {
            switch ($session_orig->recurrance_type) :
                    case 1: // Weelky

                        $start_date_correction = DateTime::createFromFormat('Y-m-d', $session_orig->start_date);

            // if the date at drop is one of the deleted dates then delete the attendance record on this date
            if ($session_date_at_drop && in_array($session_date_at_drop->format('Y-m-d'), $deletes_found)) 
            {
                DB::table('attendance')->whereSession_id($session_orig->id)->whereAbsence_date($session_date_at_drop->format('Y-m-d'))->delete();
            }

            // keep taking the finish date back while you still find events that have the deleted flag set
            for ($i = 1; $i <= 150; $i++) 
            {
                if (in_array($start_date_correction->format('Y-m-d'), $deletes_found)) 
                {
                    // keep taking the start date forwards while you still find events that have the deleted flag set (weekly events)
                    // delete the attendance row which matches the date you have stepped forward to at this point
                    DB::table('attendance')->whereSession_id($session_orig->id)->whereAbsence_date($start_date_correction->format('Y-m-d'))->delete();
                    $start_date_correction->modify('+'.$recurrance_interval.' Week');
                } 
                else 
                {
                break;
                }
            }
            break;

            case 2: // Monthly

                    $start_date_correction = DateTime::createFromFormat('Y-m-d', $session_orig->start_date)->modify('first day of this month');
            $start_date_correction->modify($input['recurrance_monthly_interval'].' '.$recurrance_days[$input['recurrance_day_single']].' of '.$start_date_correction->format('M'));

            // if the date at drop is one of the deleted dates then delete the attendance record on this date
            if ($session_date_at_drop && in_array($session_date_at_drop->format('Y-m-d'), $deletes_found)) 
            {
                DB::table('attendance')->whereSession_id($session_orig->id)->whereAbsence_date($session_date_at_drop->format('Y-m-d'))->delete();
            }

            // keep taking the start date forwards while you still find events that have the deleted flag set and deleting the appropriate attendance row (monthly events)
            for ($i = 1; $i <= 150; $i++) 
            {
                if (in_array($start_date_correction->format('Y-m-d'), $deletes_found)) {
                    // keep taking the start date forwards while you still find events that have the deleted flag set (weekly events)
                    // delete the attendance row which matches the date you have stepped forward to at this point
                    DB::table('attendance')->whereSession_id($session_orig->id)->whereAbsence_date($start_date_correction->format('Y-m-d'))->delete();
                    $start_date_correction->modify('first day of this month')->modify('+'.$recurrance_interval.' month');
                    $start_date_correction->modify($input['recurrance_monthly_interval'].' '.$recurrance_days[$input['recurrance_day_single']].' of '.$start_date_correction->format('M'));
                } 
                else 
                {
                break;
                }
            }
            break;
            default:
                endswitch;

            $date_correction['start_date'] = $start_date_correction;
            /*=================================================================================================================================>*/
        }

        return $date_correction;
    }


    public static function transferSessionAttendanceToNewSession($session, $session_orig, $session_date_param, $staff)
    {
        $attendances = SessionAttendance::whereSession_id($session_orig->id)->whereNotNull('absence')->get();

        foreach ($attendances as $attendance) {
            $attendance_new = $attendance->replicate();
            $attendance_new->session_deleted = null;
            $attendance_new->session_id = $session->id;
            $attendance_new->updated_by = $staff->id;
            $attendance_new->save();
        }
    }



    private static function finishOffExistingRecurringSet($session_date, $session, $input, $recurrance_days = false)
    {
        switch ((int) $session->recurrance_type) :
                /*============================================ REPEATS WEEKLY ==================================================>*/
        case 1:
        $finish_date = clone $session_date;

        if ($session->start_date !== $session_date->format('Y-m-d')) 
        {
            $finish_date->modify('-'. 1 * (int) $session->recurrance_interval.' Week');
        }

        if ($session->start_date === $finish_date->format('Y-m-d')) 
        {
            $session->recurrance_type = 0;
            $session->recurrance_interval = null;
            $session->ends_on = 0;
        } 
        else 
        {
            $session->ends_on = 2;
        }

        $session->recurrance_number = null;
        $session->finish_date = $finish_date->format('Y-m-d');
        // $session->save();
        break;
        /*============================================ REPEATS MONTHLY ==================================================>*/
        case 2:
        $finish_date = clone $session_date;

        if ($session->start_date !== $session_date->format('Y-m-d')) 
        {
            $finish_date->modify('first day of this month')->modify('-'. 1 * (int) $session->recurrance_interval.' Month');
        }
        $recurrance_days = [1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat', 7 => 'sun'];
        $finish_date->modify($input['recurrance_monthly_interval'].' '.$recurrance_days[$input['recurrance_day_single']].' of '.$finish_date->format('M'));

        if ($session->start_date === $finish_date->format('Y-m-d')) 
        {
            $session->recurrance_type = 0;
            $session->recurrance_interval = null;
            $session->recurrance_monthly_interval = null;

            $session->ends_on = 0;
        } 
        else 
        {
            $session->ends_on = 2;
        }

        $session->recurrance_number = null;
        $session->finish_date = $finish_date->format('Y-m-d');
        // $session->save();
        break;
        endswitch;

        return $session;
    }

    private static function correctInputStartDateRepeatWeekly($start_date, $day_no)
    {
        $diff = $day_no - $start_date->format('N');
        $actual_start_date = clone $start_date;
        $actual_start_date->modify($diff.' Day');
        if ($actual_start_date->format('Y-m-d') < $start_date->format('Y-m-d')) {
            $actual_start_date->modify('+1 Week');
        }

        return $actual_start_date;
    }

    private static function calculateFinishDateFromOccurances($actual_start_date, $input, $recurrance_days = false)
    {
        $recurrance_number = (int) $input['recurrance_number'];

        if ($recurrance_number < 1) {
            die('recurrance number is not a postive integer');
        }

        switch ((int) $input['recurrance_type']) :
            /*============================================ REPEATS WEEKLY ==================================================>*/
            case 1:
                $finish_date = clone $actual_start_date;
        $multiplier = (int) $input['recurrance_interval'] * ($recurrance_number - 1);
        $finish_date->modify('+ '.($multiplier).' Week');
        break;
        case 2:
                $finish_date = clone $actual_start_date;
        $multiplier = (int) $input['recurrance_interval'] * ($recurrance_number - 1);
        $finish_date->modify('first day of this month');
        $finish_date->modify('+ '.($multiplier).' Month');
        $finish_date->modify($input['recurrance_monthly_interval'].' '.$recurrance_days[$input['recurrance_day_single']].' of '.$finish_date->format('M'));
        break;
        endswitch;

        return $finish_date->format('Y-m-d');
    }

    private static function correctInputFinishDateRepeatWeekly($finish_date, $day_no, $input)
    {
        $diff = $day_no - $finish_date->format('N');
        $actual_finish_date = clone $finish_date;
        $actual_finish_date->modify($diff.' Day');

        if ($actual_finish_date->format('Y-m-d') > $finish_date->format('Y-m-d')) {
            $actual_finish_date->modify('-'.(int) $input['recurrance_interval'].' Week');
        }

        return $actual_finish_date;
    }





}