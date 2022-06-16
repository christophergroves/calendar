<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CalendarService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use DateTime;
use stdClass;

class SessionController extends Controller
{

    /**
     * Display session content for the calendar.
     *
     * @return \Illuminate\Http\Response
     */
    public function content(Request $request)
    {
        $user = User::where('id',$request->all()['userId'])->firstOrFail();
        $calendar_data = CalendarService::getMonth($user);
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
        dd($request->all());
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
     * Save the specified resource.
     *
     * @param  \App\Models\Session  $session
     * @return View
     */
    public function saveEdit($fpr, $view_type, $service_user_id, $session_id = false, $edit_action = false, $session_date = false, $changed_to_instance = false)
    {

        /*=========== validate with rules and redirect back on fail =========>*/
        $rules = [
            'activity_id' => 'required|alpha_number',
            'start_date' => 'date_format_uk',
            'finish_date' => 'date_format_uk',
            'session_date' => 'date_format_uk',
            'start_time' => 'time',
            'finish_time' => 'time',
            'hours' => 'integer',
            'recurrance_type' => 'integer',
            'recurrance_interval' => 'integer',
            'recurrance_number' => 'integer',
            'recurrance_day_single' => 'integer',
            'recurrance_monthly_interval' => 'alpha',
            ];
        $errors = SiteService::validateForm($rules);
        if ($errors) {
            Log::error('Path:'.Request::path().' Error: '.'Form validation errors!');
        }
        /******************************************************************************************/

        $staff = Session::get('staff');

        if ($view_type === 'Tutor') {
            $service_user = SrvUsrNav::getServiceUser($service_user_id, 'short', false, false, false, false);
        } else {
            $service_user = SrvUsrNav::getServiceUser(false, 'short');
        }

        $input = request()->all();
        $input = SiteService::trim($input);

        /*==================================== If session is quick edit =============================>*/
        if (! is_numeric($input['activity_id'])) {
            $input = CalendarService::saveQuickAddActivity($service_user, $input, $staff);
        }
        /******************************************************************************************/

        if (! array_key_exists('recurrance_type', $input)) {
            $input['recurrance_type'] = 0;
        }

        if ($edit_action === 'edit-instance') {
            CalendarService::saveInstance($input, $session_id, $session_date, $staff, $service_user);
        } else {
            switch ((int) $input['recurrance_type']) :
            /*============================================ NO REPEATS ==================================================>*/
            case 0:
                                CalendarService::saveNoRepeats($input, $session_id, $session_date, $staff, $service_user, $changed_to_instance);
            break;
            /*============================================ REPEATS WEEKLY ==================================================>*/
            case 1:
                                CalendarService::saveRepeatWeekly($input, $session_id, $edit_action, $session_date, $staff, $service_user);
            break;
            /*============================================ REPEATS MONTHLY ==================================================>*/
            case 2:
                                CalendarService::saveRepeatMonthly($input, $session_id, $edit_action, $session_date, $staff, $service_user);
            break;
            default:
                    endswitch;
        }
    }


    /**
     * Get content for the edit dialog .
     *
     * @param  \App\Models\Session  $session
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


        // return '<p>Returned HTML from get form html</p>';

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



    
    public function getEditDialogContent2($user_id, $session_id = false, $edit_action = false, $session_date = false, $activity_id = false)
    {

        // dd('hello from the getEditDialogContent method');
        $staff = Session::get('staff');

        

        $service = Session::get('service');
        $recurrance_types = [0 =>'None (Once on Session Date)', 1 =>'Weekly', 2 =>'Monthly'];
        $recurrance_days = [1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat', 7 => 'sun'];
        $disabled_elements = [];

        $start_date_label = 'Start date';

        $session_date = DateTime::createFromFormat('Y-m-d', $session_date);

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

        $activities = CalendarService::getActivitiesEditList($service_user);

        dd($activities);

        $hours = [''=>'Select hours ...', 0=>'0', 1=>'1', 2=>'2', 3=>'3', 4=>'4', 8=>'8', 12=>'12'];

        if (! $session_id) {
            $session = new SessionModel;
            $session->activity_id = $activity_id;
        } else {
            $session = DB::table('sessions')
                            ->select('sessions.*', DB::raw("DATE_FORMAT(start_time,'%H:%i') as start_time"), DB::raw("DATE_FORMAT(finish_time,'%H:%i') as finish_time"))
                            ->whereId($session_id)->first();
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


        switch ($edit_action) :
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

        $session_days = [];
        for ($i = 1; $i < 8; $i++) {
            if ((int) $session->session_day === $i) {
                $session_days[$i] = true;
            } else {
                $session_days[$i] = false;
            }
        }

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
                        ->with('edit_action', $edit_action)
                        ->with('session_date', $session_date->format('Y-m-d'))
                        ->with('start_date_label', $start_date_label)
                        ->with('edit_dialog_title', $edit_dialog_title)
                        ->with('disabled_elements', $disabled_elements)
                        ->with('ends_on', $ends_on)
                        ->with('view_type', $view_type);
    }

}
