<?php 

namespace App\Services;

use App\Http\Resources\UserResource;
use DateTime;
use stdClass;
use Illuminate\Support\Facades\DB;

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



    public static function getReviews($params)
    {
        $join_status = false;
        $service_ids = [];
        $order_by = '';
        $where = ' ';

        /*======================= either one service_id or multiple service_ids =====================>*/
        foreach ($params['service_id'] as $service_id) {
            $service_ids[] = $service_id;
        }
        /*============================================================*/

        if ($params['sort']) {
            switch ($params['sort']) :
          case 1: $order_by .= 'contacts.surname,contacts.firstname';
            break;
            case 2: $order_by .= 'staff.surname,staff.firstname';
            break;
            case 3: $order_by .= 'TSubSD.start_date DESC';
            break;
            case 4: $order_by .= 'TSubLD.leave_date DESC';
            break;
            case 5: $order_by .= 'contacts.surname,contacts.firstname'; // dummy sort because true sort happens later
            break;
            endswitch;
            $order_by .= ',contacts.surname,contacts.firstname ';
        }

        $objectives_in_progress_count =
        '(SELECT
          Count(*) as objectives_count,
          objectives.service_user_id
          FROM
          objectives
          WHERE
          objectives.objective_status_id = 3
          GROUP BY objectives.service_user_id
          ) AS objectives_count';

        $service_user_start_date =
        '(SELECT DISTINCT
        status.service_user_id,
        status.status_date AS start_date
        FROM 
        status
        WHERE 
        status.status_type_id IN (1)) AS TSubSD';

        $service_user_leave_date =
        '(SELECT DISTINCT
        status.service_user_id,
        status.status_date AS leave_date
        FROM 
        status
        WHERE 
        status.status_type_id IN (3,4)) AS TSubLD';

        $service_users = ServiceUser::with('objective_reviews')
          ->with('objectives')
          ->with('follow_ups')

          ->select(
            'service_users.id',
            'contacts.firstname',
            'contacts.surname',
            'TSubSD.start_date',
            'TSubLD.leave_date',
            'dates_misc.date_initial_action_plan',
            'objectives_count.objectives_count',

            DB::raw("CONCAT_WS(' ',service_names.service_name,service_areas.service_area) AS service_area"),
            DB::raw("CONCAT_WS(' ',staff.firstname,staff.surname) AS staff")
          )

          ->join('contacts', function ($join) {
              $join->on('contacts.service_user_id', '=', 'service_users.id');
              $join->on('contacts.contact_type_id', '=', DB::raw('1'));
          })

          ->leftJoin(DB::raw($service_user_start_date), function ($join) {
              $join->on('TSubSD.service_user_id', '=', 'contacts.service_user_id');
          })

          ->leftJoin(DB::raw($service_user_leave_date), function ($join) {
              $join->on('TSubLD.service_user_id', '=', 'service_users.id');
          })

          ->join('service_users2services', function ($join) {
              $join->on('service_users2services.service_user_id', '=', 'contacts.service_user_id');
              $join->on('service_users2services.active', '=', DB::raw('1'));
          })

          ->join('services', 'services.id', '=', 'service_users2services.service_id')
          ->join('service_areas', 'service_areas.id', '=', 'services.service_area_id')
          ->join('service_names', 'service_names.id', '=', 'services.service_name_id')

          ->leftJoin('service_users2staff', function ($join) {
              $join->on('service_users2staff.service_user_id', '=', 'contacts.service_user_id');
              $join->on('service_users2staff.active', '=', DB::raw('1'));
          })

          ->leftJoin('staff', 'staff.id', '=', 'service_users2staff.staff_id')

          ->join('status', function ($join) {
              $join->on('status.service_user_id', '=', 'contacts.service_user_id');
              $join->on('status.active', '=', DB::raw('1'));
          })

          ->leftJoin('trust_areas', 'trust_areas.id', '=', 'service_users.trust_area_id')
          ->leftJoin('trusts', 'trusts.id', '=', 'trust_areas.trust_id')
          ->leftJoin('trust_regions', 'trust_regions.id', '=', 'trust_areas.trust_region_id')

          ->leftJoin('dates_misc', 'dates_misc.service_user_id', '=', 'service_users.id')

          ->leftJoin(DB::raw($objectives_in_progress_count), function ($join) {
              $join->on('objectives_count.service_user_id', '=', 'service_users.id');
          })

          ->whereIn('service_users2services.service_id', $service_ids)
          ->where('status.status_type_id', $params['status']);

        /*================== This deals with the service grouping selections ===============>*/
        if ($params['service_name_id']) {
            $service_users = $service_users
            ->where('services.service_name_id', '=', $params['service_name_id']);
        }

        if ($params['filter']) {
            switch ($params['filter'][0]) :
              case 'a':
                $service_users = $service_users->where('trust_areas.id', substr($params['filter'], 1));
            break;
            case 't':
                $service_users = $service_users->where('trusts.id', substr($params['filter'], 1));
            break;
            case 'p':
                $service_users = $service_users->where('service_users2staff.staff_id', substr($params['filter'], 1));
            break;
            endswitch;
        }

        if ($params['staff_id']) {
            $service_users = $service_users->where('service_users2staff.staff_id', $params['staff_id']);
        }

        $service_users = $service_users

          ->groupBy('service_users.id')

          ->orderByRaw($order_by)

          ->get();

        // This portion is purely so that we can sort by 'review_due' (which is added after collection from db)
        if ((int) $params['status'] === 1) {
            foreach ($service_users as $service_user) {
                $review_due = false;
                foreach ($service_user->objective_reviews as $review) {
                    $review_due = $review->review_date;
                }
                if ($review_due) {
                    $review_due = DateTime::createFromFormat('Y-m-d', $review_due);
                    $review_due->modify('+6 Month');
                } elseif ($service_user->date_initial_action_plan) {
                    $review_due = DateTime::createFromFormat('Y-m-d', $service_user->date_initial_action_plan);
                    $review_due->modify('+6 Month');
                }
                $service_user->review_due = $review_due ? $review_due->format('Y-m-d') : null;
            }
        }

        // Do the actual sort in the elequent collection object
        if ($params['sort'] === '5' && (int) $params['status'] === 1) {
            $service_users = $service_users->sortByDesc('review_due');
        }

        return $service_users;
    }























}