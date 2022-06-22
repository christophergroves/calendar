<?php

namespace App\Http\Controllers;

use DateTime;
use stdClass;
use App\Models\User;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CalendarService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;

class SessionController extends Controller
{

    /**
     * Display session content for the calendar.
     *
     * @return \Illuminate\Http\Response
     */
    public function content(Request $request)
    {
        $request = $request->all();
        $user = User::where('id',$request['userId'])->firstOrFail();
        $calendar_data = CalendarService::getMonth($user,$request);
        return $calendar_data;
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $request;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSessionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSessionRequest $request)
    {
        $request = $request->all();
        $user = User::where('id',$request['userId'])->firstOrFail();

        if (! array_key_exists('recurrance_type', $request)) {
            $request['recurrance_type'] = 0;
        }

        if ($request['action'] === 'edit-instance') {
            CalendarService::saveInstance($request, $user);
        } 
        else 
        {
            switch ((int)$request['recurrance_type']) :
            /*============================================ NO REPEATS ==================================================>*/
            case 0:
                    CalendarService::saveNoRepeats($request, $user, $changed_to_instance=false);
            break;
            /*============================================ REPEATS WEEKLY ==================================================>*/
            case 1:
                   CalendarService::saveRepeatWeekly($request, $user);
            break;
            /*============================================ REPEATS MONTHLY ==================================================>*/
            case 2:
                    // CalendarService::saveRepeatMonthly($input, $session_id, $edit_action, $session_date, $staff, $service_user);
            break;
            default:
            endswitch;
        }

        return response('success',200);

    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function show(Session $session)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function edit(Session $session)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSessionRequest  $request
     * @param  \App\Models\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSessionRequest $request, Session $session)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function destroy(Session $session)
    {
        //
    }




    /**
     * Get content for the edit dialog .
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getEditDialogContent(Request $request)
    {
        $request = $request->all();

        $user = User::where('id',$request['userId'])->firstOrFail();

        $recurrance_types = [0 =>'None (Once on Session Date)', 1 =>'Weekly', 2 =>'Monthly'];
        $recurrance_days = [1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat', 7 => 'sun'];
        
        $session_date = DateTime::createFromFormat('Y-m-d', $request['sessionDate']);

        $recurrance_monthly_intervals = [
            'first' => 'first',
            'second' => 'second',
            'third' => 'third',
            'fourth' => 'fourth',
            'last' => 'last',
        ];

        $recurrance_intervals = [1 => 1];
        for ($i = 2; $i <= 12; $i++) {
            array_push($recurrance_intervals, $i);
        }

        $hours = [''=>'Select hours ...', 0=>'0', 1=>'1', 2=>'2', 3=>'3', 4=>'4', 8=>'8', 12=>'12'];
        $disabled_elements = [];
        $errors = false;
        

        if (!$request['sessionId']) 
        {
            $session = new Session;
            $session->activity_id = $request['activityId'];
        } else {
            $session = Session::select('sessions.*', DB::raw("DATE_FORMAT(start_time,'%H:%i') as start_time"), DB::raw("DATE_FORMAT(finish_time,'%H:%i') as finish_time"))->whereId($request['sessionId'])->first();
        }

        $ends_on = [0=>false, 1=>false, 2=>false];
        switch ($session->ends_on) :
        case 0:
            $ends_on[0] = true;
        break;
        case 1:
            $ends_on[1] = true;
        break;
        case 2:
            $ends_on[2] = true;
        break;
        endswitch;

        $session_days = [];
        for ($i = 1; $i < 8; $i++) {
            if ((int) $session->session_day === $i) {
                $session_days[$i] = true;
            } else {
                $session_days[$i] = false;
            }
        }

        switch ($request['action']) :
        case 'edit-new':
            $edit_dialog_title = 'Create New Session (Start Date: '.$session_date->format('d/m/Y').')';
        $session->start_date = $session_date->format('Y-m-d');
        break;
        case 'edit-all':
             $edit_dialog_title = 'Edit Recurring Session (Date: '.$session_date->format('d/m/Y').')';
        break;
        case 'edit-one-off':
            $edit_dialog_title = 'Edit One Off Session (Date: '.$session_date->format('d/m/Y').')';
        break;
        case 'edit-instance':
            $start_date_label = 'Date';
            $edit_dialog_title = 'Edit Instance &nbsp;(Previously Part Of Recurring Set)';
            $session->start_date = $session_date->format('Y-m-d');
        $session->recurrance_type = 0;
        break;
        case 'edit-now-on':
            $start_date_label = 'Date';
            $edit_dialog_title = 'Edit from now on &nbsp;(Recurring Session)';
            $recurrance_types = [1 =>'Weekly', 2 =>'Monthly'];
            $session->start_date = $session_date->format('Y-m-d');
            $session->session_day = false;
        break;
        default:
        endswitch;


        $activities = CalendarService::getActivitiesEditList($user);


         return view('main.calendar.calendar_edit_dialog_content')
            ->with('user', $user)
            ->with('recurrance_types', $recurrance_types)
            ->with('recurrance_days', $recurrance_days)
            ->with('recurrance_monthly_intervals', $recurrance_monthly_intervals)
            ->with('recurrance_intervals', $recurrance_intervals)
            ->with('activities', $activities)
            ->with('session', $session)
            ->with('session_days', $session_days)
            ->with('hours', $hours)
            ->with('edit_action', $request['action'])
            ->with('session_date', $session_date->format('Y-m-d'))
            ->with('start_date_label', 'Start date')
            ->with('edit_dialog_title', $edit_dialog_title)
            ->with('errors',$errors)
            ->with('disabled_elements', $disabled_elements)
            ->with('ends_on', $ends_on);

    }


}
