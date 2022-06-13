<?php 

namespace App\Services;

use DateTime;
use stdClass;
use Illuminate\Support\Facades\DB;
// use PhpParser\Builder\Class_;

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
                        if (! self::checkIncludeOccurance($row)) 
                        {
                            continue;
                        }

                        if ($row->session_deleted === '1') {
                            $background_color = '#F5F5F5';
                        } else {
                            switch ($row->activity_type_category) :
                            case 'Social':
                                            $background_color = '#3A87AD';
                            break;
                            case 'Training':
                                            $background_color = '#3C967B';
                            break;
                            case 'Practical':
                                             // $background_color = '#914D14';
                                             $background_color = '#9B5115';

                            break;
                            case 'WorkExp':
                                            // $background_color = '#669933';
                                             $background_color = '#699E35';

                            break;
                            case 'ProgPlanning':
                                            $background_color = '#8092A3';
                            break;
                            default:
                                            $background_color = '#3A87AD';
                            endswitch;
                        }

                        $event = new stdClass();



                        






                        $event->id = strip_tags($row->sessID);

                        $event->activity_id = strip_tags($row->activity_id);

                        // $event->activity_class = strip_tags($row->activity_class);

                        $event->session_day = strip_tags($row->sessDay);

                        $event->title = strip_tags($row->activity);

                        $event->start = strip_tags($row->session_date);
                        if ($row->start_time) {
                            $event->start .= 'T'.strip_tags($row->start_time);
                        }

                        $event->session_start_date = strip_tags($row->session_start_date_uk);
                        $event->session_finish_date = strip_tags($row->session_finish_date_uk);

                        $event->start_time = strip_tags($row->start_time);
                        $event->finish_time = strip_tags($row->finish_time);
                        $event->hours = strip_tags($row->hours);

                        $event->parent_id = strip_tags($row->parent_id);
                        $event->parent_id_child = strip_tags($row->parent_id_child);
                        $event->attendance = strip_tags($row->attendance);
                        $event->attendance_notes = strip_tags($row->attendance_notes);
                        $event->session_deleted = strip_tags($row->session_deleted);
                        $event->recurrance_type = strip_tags($row->recurrance_type);
                        $event->recurrance_interval = strip_tags($row->recurrance_interval);
                        $event->recurrance_number = strip_tags($row->recurrance_number);

                        $event->textColor = $row->attendance ? '#FFD20F' : 'white';

                        $event->backgroundColor = $background_color;
                        $event->borderColor = $background_color;


                        $event->tutor = strip_tags($row->tutor);
                        $event->case_officer = strip_tags($row->case_officer);

                        $event->service_user_id = strip_tags($row->service_user_id);
                        $event->service_user_name = strip_tags($row->service_user_name);

                        $event->transport_provider = strip_tags($row->transport_provider);

                        $event->updated_activity = strip_tags($row->updated_by_activity.' '.$row->updated_at_activity);
                        $event->updated_session = strip_tags($row->updated_by_session.' '.$row->updated_at_session);
                        $event->updated_attendance = strip_tags($row->updated_by_attendance.' '.$row->updated_at_attendance);

                        $event->week_beginning = $week_beginning->format('Y-m-d');
                        $event->week_ending = $week_ending->format('Y-m-d');

                        $events[] = $event;
                    }
                }

                $week_beginning->modify('+1 Week');
                $week_ending->modify('+1 Week');
            }



            // $time_end = microtime(true);
            //dividing with 60 will give the execution time in minutes other wise seconds
            // $execution_time = substr($time_end - $time_start,0,6);

            // substr($string,0,10)
            //execution time of the script
            // echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
        }

  
        $events = ['events'=>$events, 'user'=>$user];

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

        // dd($indlude);

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
        activity_types.category as activity_type_category,
   





        CONCAT(activity_types.name,' @ ',venues.name,' - ',activities.description) AS activity,

      

      
        DATE_ADD(:week_beginning, INTERVAL sessions.session_day - 1 DAY) as session_date,

        DATE_FORMAT(sessions.start_date,'%d/%m/%Y') AS session_start_date_uk,
        DATE_FORMAT(sessions.finish_date,'%d/%m/%Y') AS session_finish_date_uk,


        -- sessions.start_date as session_start_date,
        -- sessions.finish_date as session_finish_date,



        DATE_FORMAT(start_time, '%H:%i') as start_time,
        DATE_FORMAT(finish_time, '%H:%i') as finish_time,
        -- sessions.start_date,
        -- sessions.finish_date,
        session_attendances.absence as attendance,
        session_attendances.attendance_notes,
        session_attendances.absense_date,
        sessions.hours,

    
        TSubChild.parent_id_child
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

        // dd($allrows);

        return $allrows;
        

    }
























}