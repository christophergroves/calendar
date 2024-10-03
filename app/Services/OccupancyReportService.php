<?php

namespace App\Services\Services\Reports;

use App\Models\ServiceUser;
use Illuminate\Support\Facades\DB;

require_once app_path().'/Includes/constants/sql_constants.php';

class OccupancyReportService
{
    public static function getOccupancyPeriod($params)
    {
        $bind = [':period_from' => $params['period_from'], ':period_to' => $params['period_to']];

        $where = ' ( '; // remember the first bracket because we are using OR for the service ids
        $join = ' ';
        $order_by = ' ORDER BY ';

        /*================== create the WHERE and bind for either one service_id or multiple service_ids (TO KEEP QUERY PARAMATERISED!!) ===============>*/
        foreach ($params['service_id'] as $key => $service_id) {
            $bind[':service_id'.$key] = $service_id;
            $where .= ' service_users2services.service_id = :service_id'.$key.' OR';
        }
        $where = rtrim($where, 'OR').') '; //take the trailing OR off and add the final bracket
        /*============================================================*/

        /*================== This deals with the service grouping selections ===============>*/
        if ($params['service_name_id']) {
            $where .= ' AND services.service_name_id = :service_name_id ';
            $bind['service_name_id'] = $params['service_name_id'];
        }

        if ($params['filter']) {
            switch ($params['filter'][0]):
                    case 'a':
                        $bind[':trust_area_id'] = substr($params['filter'], 1);
            $where .= ' AND trust_areas.id = :trust_area_id ';
            break;
            case 't':
                        $bind[':trust_id'] = substr($params['filter'], 1);
            $where .= ' AND trusts.id = :trust_id ';
            break;
            case 'p':
                        $bind[':project_officer_staff_id'] = substr($params['filter'], 1);
            $where .= ' AND staff.id = :project_officer_staff_id ';
            break;
            endswitch;
        }

        if ($params['status']) {
            switch ($params['status']):
                    case 'Starter':
                        $join = INNER_JOIN_FILTER_STATUS_STARTER;
            break;
            case 1:
                        $join = INNER_JOIN_FILTER_STATUS_CURRENT;
            break;
            case 3:
                        $join = INNER_JOIN_FILTER_STATUS_LEAVER;
            break;
            case 5:
                        $join = INNER_JOIN_FILTER_STATUS_REFERRAL_READY_TO_START;
            break;
            case 6:
                        $join = INNER_JOIN_FILTER_STATUS_REFERRAL_NOT_READY_TO_START;
            break;
            case 2:
                        $join = INNER_JOIN_FILTER_STATUS_UNSUCCESSFUL_REFERRAL;
            break;
            endswitch;
        }

        if ($params['sort']) {
            switch ($params['sort']):
                    case 1: $order_by .= 'contacts.surname,contacts.firstname';
            break;
            case 2: $order_by .= 'trusts.trust_name,trust_regions.trust_region_name';
            break;
            case 3: $order_by .= 'staff.surname,staff.firstname';
            break;
            case 4: $order_by .= 'dates_misc.referral_received_date DESC';
            break;
            case 5: $order_by .= 'service_users.date_of_birth DESC';
            break;
            case 6: $order_by .= 'TSubSD.start_date DESC';
            break;
            case 7: $order_by .= 'TSubLD.leave_date DESC';
            break;
            case 8: $order_by .= 'TSubCS.status_type';
            break;
            case 9: $order_by .= 'service_users.referrer_name';
            break;
            case 10: $order_by .= 'referrer_orgs.organisation_name';
            break;
            case 11: $order_by .= 'service_users.disability_category';
            break;
            case 12: $order_by .= 'service_names.service_name, service_areas.service_area';
            break;
            endswitch;
            $order_by .= ',contacts.surname,contacts.firstname ';
        }

        $query = "SELECT 
				contacts.firstname,
				contacts.surname,
				service_users.gender,
				contacts.town,
				CONCAT_WS(' ',service_names.service_name,service_areas.service_area) AS service_area,

				CONCAT(trusts.trust_name,' (',trust_regions.trust_region_name, ')') AS trust_area,
				DATE_FORMAT(TSubSD.start_date,'%d/%m/%Y') AS start_date,
				DATE_FORMAT(TSubLD.leave_date,'%d/%m/%Y') AS leave_date,


				TSubCS.status_type,

				
				DATE_FORMAT(dates_misc.referral_received_date,'%d/%m/%Y') AS referral_received_date,
				CONCAT(staff.firstname,' ',staff.surname) as project_officer,
				DATE_FORMAT(service_users.date_of_birth,'%d/%m/%Y') AS date_of_birth,


				TIMESTAMPDIFF(YEAR,service_users.date_of_birth,TSubSD.start_date) as age_at_start,

				TIMESTAMPDIFF(YEAR,service_users.date_of_birth,CURDATE()) as age_current,
				referrer_orgs.organisation_name,
				service_users.referrer_name,
				service_users.disability_category
				

				FROM
				service_users

				INNER JOIN contacts ON contacts.service_user_id = service_users.id AND contacts.contact_type_id = 1
				INNER JOIN service_users2services ON service_users2services.service_user_id = service_users.id AND service_users2services.active = 1

				INNER JOIN services ON service_users2services.service_id = services.id
				INNER JOIN service_areas ON service_areas.id = services.service_area_id
				INNER JOIN service_names ON service_names.id = services.service_name_id		

				LEFT JOIN dates_misc ON dates_misc.service_user_id = service_users.id

				LEFT JOIN service_users2staff ON service_users2staff.service_user_id = service_users.id AND service_users2staff.active = 1 
	
				LEFT JOIN staff ON staff.id = service_users2staff.staff_id

				LEFT JOIN trust_areas ON trust_areas.id = service_users.trust_area_id
				LEFT JOIN trusts ON trusts.id = trust_areas.trust_id
				LEFT JOIN trust_regions ON trust_regions.id = trust_areas.trust_region_id

				LEFT JOIN referrer_orgs ON referrer_orgs.id = service_users.referrer_org_id ".
                LEFT_JOIN_GET_CURRENT_STATUS.
                LEFT_JOIN_GET_START_DATE.
                LEFT_JOIN_GET_LEAVE_DATE.
                $join.
                'WHERE '.
                $where.
                'GROUP BY service_users.id '.
                $order_by;

        $result = DB::select($query, $bind);

        return $result;
    }

    public static function getOccupancy($params)
    {
        $bind = [':status_type_id' => $params['status']];

        $where = ' ( '; // remember the first bracket because we are using OR for the service ids
        $join = ' ';
        $order_by = ' ORDER BY ';

        /*======================= create the WHERE and bind for either one service_id or multiple service_ids =====================>*/
        foreach ($params['service_id'] as $key => $service_id) {
            $bind[':service_id'.$key] = $service_id;
            $where .= ' service_users2services.service_id = :service_id'.$key.' OR';
        }
        $where = rtrim($where, 'OR').') ';
        /*============================================================*/

        /*================== This deals with the service grouping selections ===============>*/
        if ($params['service_name_id']) {
            $where .= ' AND services.service_name_id = :service_name_id ';
            $bind['service_name_id'] = $params['service_name_id'];
        }

        if ($params['filter']) {
            switch ($params['filter'][0]):
                    case 'a':
                        $bind[':trust_area_id'] = substr($params['filter'], 1);
            $where .= ' AND trust_areas.id = :trust_area_id ';
            break;
            case 't':
                        $bind[':trust_id'] = substr($params['filter'], 1);
            $where .= ' AND trusts.id = :trust_id ';
            break;
            case 'p':
                        $bind[':project_officer_staff_id'] = substr($params['filter'], 1);
            $where .= ' AND staff.id = :project_officer_staff_id ';
            break;
            endswitch;
        }

        $where .= ' AND status.status_type_id = :status_type_id AND status.active = 1 ';

        // if ($params['status']) {

        // 	switch($params['status']):
        // 		case 1:
        // 			$where .= ' AND status.status_type_id = 1 AND status.active = 1 ';

        // 			// $join = INNER_JOIN_FILTER_STATUS_CURRENT;
        // 			break;
        // 		case 3:
        // 			$where .= ' AND status.status_type_id = 3 AND status.active = 1 ';
        // 			// $join = INNER_JOIN_FILTER_STATUS_COMPLETER;
        // 			break;
        // 		case 4:
        // 			$where .= ' AND status.status_type_id = 4 AND status.active = 1 ';
        // 			// $join = INNER_JOIN_FILTER_STATUS_EARLY_LEAVER;
        // 			break;
        // 		case 5:
        // 			$where .= ' AND status.status_type_id = 5 AND status.active = 1 ';
        // 			// $join = INNER_JOIN_FILTER_STATUS_REFERRAL_READY_TO_START;
        // 			break;
        // 		case 6:
        // 			$where .= ' AND status.status_type_id = 6 AND status.active = 1 ';
        // 			// $join = INNER_JOIN_FILTER_STATUS_REFERRAL_NOT_READY_TO_START;
        // 			break;
        // 		case 2:
        // 			$where .= ' AND status.status_type_id = 2 AND status.active = 1 ';
        // 			// $join = INNER_JOIN_FILTER_STATUS_UNSUCCESSFUL_REFERRAL;
        // 			break;

        // 			// $bind[":status_type_id"] = $params['status'];
        // 			// $where .= " AND (status.status_type_id = :status_type_id AND status.active = 1 AND
        // 			// 		`status`.status_date  >  DATE_ADD(NOW(),INTERVAL " . LEAVER_DATE_CUT_OFF . " MONTH)) ";
        // 		// default:

        // 		// 	$bind[":status_type_id"] = $params['status'];
        // 		// 	$where .= " AND (status.status_type_id = :status_type_id AND status.active = 1) ";
        // 	endswitch;
        // }

        if ($params['sort']) {
            switch ($params['sort']):
                    case 1: $order_by .= 'contacts.surname,contacts.firstname';
            break;
            case 2: $order_by .= 'trusts.trust_name,trust_regions.trust_region_name';
            break;
            case 3: $order_by .= 'staff.surname,staff.firstname';
            break;
            case 4: $order_by .= 'dates_misc.referral_received_date DESC';
            break;
            case 5: $order_by .= 'service_users.date_of_birth DESC';
            break;
            case 6: $order_by .= 'TSubSD.start_date DESC';
            break;
            case 7: $order_by .= 'TSubLD.leave_date DESC';
            break;
            case 8: $order_by .= 'TSubCS.status_type';
            break;
            case 9: $order_by .= 'service_users.referrer_name';
            break;
            case 10: $order_by .= 'referrer_orgs.organisation_name';
            break;
            case 11: $order_by .= 'service_users.disability_category';
            break;
            case 12: $order_by .= 'service_names.service_name, service_areas.service_area';
            break;
            endswitch;
            $order_by .= ',contacts.surname,contacts.firstname ';
        }

        $query = "SELECT 
				contacts.firstname,
				contacts.surname,
				service_users.gender,
				contacts.town,
				CONCAT_WS(' ',service_names.service_name,service_areas.service_area) AS service_area,

				CONCAT(trusts.trust_name,'-',trust_regions.trust_region_name) AS trust_area,
				DATE_FORMAT(TSubSD.start_date,'%d/%m/%Y') AS start_date,
				DATE_FORMAT(TSubLD.leave_date,'%d/%m/%Y') AS leave_date,

				

				TSubCS.status_type,

				
				DATE_FORMAT(dates_misc.referral_received_date,'%d/%m/%Y') AS referral_received_date,
				CONCAT(staff.firstname,' ',staff.surname) as project_officer,
				DATE_FORMAT(service_users.date_of_birth,'%d/%m/%Y') AS date_of_birth,
				
			

				TIMESTAMPDIFF(YEAR,service_users.date_of_birth,TSubSD.start_date) as age_at_start,

				TIMESTAMPDIFF(YEAR,service_users.date_of_birth,CURDATE()) as age_current,
				referrer_orgs.organisation_name,
				service_users.referrer_name,
				service_users.disability_category
				
				

				FROM
				service_users

				INNER JOIN status ON status.service_user_id = service_users.id AND status.active=1

				INNER JOIN contacts ON contacts.service_user_id = service_users.id AND contacts.contact_type_id = 1
				LEFT JOIN service_users2services ON service_users2services.service_user_id = service_users.id AND service_users2services.active = 1				
				LEFT JOIN services ON service_users2services.service_id = services.id
				LEFT JOIN service_areas ON service_areas.id = services.service_area_id
				LEFT JOIN service_names ON service_names.id = services.service_name_id	

				LEFT JOIN dates_misc ON dates_misc.service_user_id = service_users.id

				LEFT JOIN service_users2staff ON service_users2staff.service_user_id = service_users.id AND service_users2staff.active = 1 
				
		
	
				LEFT JOIN staff ON staff.id = service_users2staff.staff_id AND service_users2staff.active = 1

				LEFT JOIN trust_areas ON trust_areas.id = service_users.trust_area_id
				LEFT JOIN trusts ON trusts.id = trust_areas.trust_id
				LEFT JOIN trust_regions ON trust_regions.id = trust_areas.trust_region_id

				LEFT JOIN referrer_orgs ON referrer_orgs.id = service_users.referrer_org_id ".
                LEFT_JOIN_GET_CURRENT_STATUS.
                LEFT_JOIN_GET_START_DATE.
                LEFT_JOIN_GET_LEAVE_DATE.

                'WHERE  '.
                $where.
                'GROUP BY service_users.id '.
                $order_by;

        // dd($query);

        $result = DB::select($query, $bind);

        // dd($result);

        return $result;
    }

    public static function getOccupancy2($params)
    {
        // SiteService::dd($params['status']);

        // SiteService::dd($params);

        $service_ids = [];
        $order_by = '';
        $where = ' ';

        /*======================= either one service_id or multiple service_ids =====================>*/
        foreach ($params['service_id'] as $service_id) {
            $service_ids[] = $service_id;
        }
        /*============================================================*/

        if ($params['sort']) {
            switch ($params['sort']):
                case 1: $order_by .= 'contacts.surname,contacts.firstname';
            break;
            case 2: $order_by .= 'trusts.trust_name,trust_regions.trust_region_name';
            break;
            case 3: $order_by .= 'staff.surname,staff.firstname';
            break;
            case 4: $order_by .= 'dates_misc.referral_received_date DESC';
            break;
            case 5: $order_by .= 'service_users.date_of_birth DESC';
            break;
            case 6: $order_by .= 'TSubSD.service_user_start_date DESC';
            break;
            case 7: $order_by .= 'TSubLD.service_user_leave_date DESC';
            break;
            case 8: $order_by .= 'TSubCS.status_type';
            break;
            case 9: $order_by .= 'referrer_orgs.organisation_name';
            break;
            case 10: $order_by .= 'service_users.disability_category';
            break;
            case 11: $order_by .= 'service_names.service_name, service_areas.service_area';
            break;
            endswitch;
            $order_by .= ',contacts.surname,contacts.firstname ';
        }

        if ($params['report_type'] === '2') {
            switch ($params['status']):
                case 'Starter':
                    $join_status = INNER_JOIN_FILTER_STATUS_STARTER;
            break;
            case 1:
                    $join_status = INNER_JOIN_FILTER_STATUS_CURRENT;
            break;
            case 3:
                    $join_status = INNER_JOIN_FILTER_STATUS_LEAVER;
            break;
            case 5:
                    $join_status = INNER_JOIN_FILTER_STATUS_REFERRAL_READY_TO_START;
            break;
            case 6:
                    $join_status = INNER_JOIN_FILTER_STATUS_REFERRAL_NOT_READY_TO_START;
            break;
            case 2:
                    $join_status = INNER_JOIN_FILTER_STATUS_UNSUCCESSFUL_REFERRAL;
            break;
            endswitch;

            // Correct the join from the constants in sql_constants so that it works with laravel's elequent (->join) *** THE PERIOD_FROM AND PERIOD_TO MUST BE IN THE ORDER THAT THE BINDINGS ARE APPLIED BELOW IN ELEQUENT !!!!
            $join_status = rtrim($join_status, 'ON TSub.id = service_users.id'); //trim off bits we don't need
            $join_status = ltrim($join_status, 'INNER JOIN'); //trim off bits we don't need
            $join_status = str_replace(':period_to', '?', $join_status); //replace with ? marks
            $join_status = str_replace(':period_from', '?', $join_status); //replace with ? marks
        }
        // elseif($params['report_type'] === '1')
        // {
        // 	switch($params['status']):
        // 		case "Current":
        // 			$where .= " (status_types.category = 'Current' AND status.active = 1) ";
        // 			break;
        // 		case "Leaver":
        // 			// $where .= " (status_types.category = 'Leaver' AND status.active = 1 AND
        // 			// 		status.status_date  >  DATE_ADD(NOW(),INTERVAL " . LEAVER_DATE_CUT_OFF . " MONTH)) ";
        // 			$where .= " (status_types.category = 'Leaver' AND status.active = 1) ";
        // 			break;
        // 		default;
        // 	endswitch;
        // }

        // Correct the join from the constants in sql_constants so that it works with laravel's elequent (->join) *** THE PERIOD_FROM AND PERIOD_TO MUST BE IN THE ORDER THAT THE BINDINGS ARE APPLIED BELOW IN ELEQUENT !!!!
        // $join_status = rtrim($join_status, 'ON TSub.id = service_users.id'); //trim off bits we don't need
        // $join_status = ltrim($join_status, 'INNER JOIN'); //trim off bits we don't need
        // $join_status = str_replace(':period_to','?',$join_status); //replace with ? marks
        // $join_status = str_replace(':period_from','?',$join_status); //replace with ? marks

        // // Correct the join from the constants in sql_constants so that it works with laravel's elequent (->join) *** THE PERIOD_FROM AND PERIOD_TO MUST BE IN THE ORDER THAT THE BINDINGS ARE APPLIED BELOW IN ELEQUENT !!!!
        // $join_status = rtrim($join_status, 'ON TSub.id = service_users.id'); //trim off bits we don't need
        // $join_status = ltrim($join_status, 'INNER JOIN'); //trim off bits we don't need
        // $join_status = str_replace(':period_to','?',$join_status); //replace with ? marks
        // $join_status = str_replace(':period_from','?',$join_status); //replace with ? marks

        // $join_status_now = LEFT_JOIN_GET_CURRENT_STATUS;
        // $join_status_now = rtrim($join_status_now, 'ON TSubCS.service_user_id = service_users.id'); //trim off bits we don't need
        // $join_status_now = ltrim($join_status_now, 'LEFT JOIN'); //trim off bits we don't need

        $service_user_status_now =
         '(SELECT DISTINCT
			status.service_user_id,
			status_types.status_type
			FROM 
			status
			LEFT JOIN status_types ON status_types.id = status.status_type_id
			WHERE 
			status.active = 1) AS TSubCS';

        $service_user_start_date =
            '(SELECT DISTINCT
			status.service_user_id,
			status.status_date AS service_user_start_date
			FROM 
			status
			WHERE 
			status.status_type_id IN (1)) AS TSubSD';

        $service_user_leave_date =
            '(SELECT DISTINCT
			status.service_user_id,
			status.status_date AS service_user_leave_date
			FROM 
			status
			WHERE 
			status.status_type_id IN (3,4)) AS TSubLD';

        // CONCAT(trusts.trust_name,' (',trust_regions.trust_region_name, ')') AS trust_area,
        // DATE_FORMAT(TSubSD.start_date,'%d/%m/%Y') AS start_date,
        // DATE_FORMAT(TSubLD.leave_date,'%d/%m/%Y') AS leave_date,

        // TSubCS.status_type,

        // DATE_FORMAT(dates_misc.referral_received_date,'%d/%m/%Y') AS referral_received_date,
        // CONCAT(staff.firstname,' ',staff.surname) as project_officer,
        // DATE_FORMAT(service_users.date_of_birth,'%d/%m/%Y') AS date_of_birth,

        // referrer_orgs.organisation_name,
        // service_users.referrer_name,
        // service_users.disability_category

        // SiteService::dd($service_ids);

        $service_users = ServiceUser::whereIn('service_users2services.service_id', $service_ids)

                ->with('outcomes')

                ->select(
                    'service_users.id',
                    'contacts.firstname',
                    'contacts.surname',
                    'service_users.gender',
                    'service_users.date_of_birth',
                    'service_users.disability_category',
                    'contacts.town',
                    'staff.id as staff_id',
                    'staff.firstname as staff_firstname',
                    'staff.surname as staff_surname',
                    'service_users2staff.date_start as staff_date_start',
                    'TSubSD.service_user_start_date',
                    'TSubLD.service_user_leave_date',
                    'service_names.service_name',
                    'service_areas.service_area',
                    'referrer_orgs.organisation_name',
                    'dates_misc.referral_received_date',
                    'dates_misc.date_initial_action_plan',
                    'TSubCS.status_type',

                    DB::raw('TIMESTAMPDIFF(YEAR,service_users.date_of_birth,TSubSD.service_user_start_date) as age_at_start'),
                    DB::raw('TIMESTAMPDIFF(YEAR,service_users.date_of_birth,CURDATE()) as age_current'),
                    DB::raw('CONCAT(trusts.trust_name," (",trust_regions.trust_region_name, ")") AS trust_area')

                )

                // ->join('status', function($join){
                //     $join->on('status.service_user_id', '=', 'service_users.id');
                //     $join->on('status.active','=',DB::raw('1'));
                // })
                // ->join('status_types','status_types.id','=','status.status_type_id')

                ->join('service_users2services', function ($join) {
                    $join->on('service_users2services.service_user_id', '=', 'service_users.id');
                    $join->on('service_users2services.active', '=', DB::raw('1'));
                })

                ->join('services', 'services.id', '=', 'service_users2services.service_id')
                ->join('service_areas', 'service_areas.id', '=', 'services.service_area_id')
                ->join('service_names', 'service_names.id', '=', 'services.service_name_id')

                ->leftJoin('dates_misc', 'dates_misc.service_user_id', '=', 'service_users.id')

                ->leftJoin('referrer_orgs', 'referrer_orgs.id', '=', 'service_users.referrer_org_id')
                ->leftJoin('trust_areas', 'trust_areas.id', '=', 'service_users.trust_area_id')
                ->leftJoin('trusts', 'trusts.id', '=', 'trust_areas.trust_id')
                ->leftJoin('trust_regions', 'trust_regions.id', '=', 'trust_areas.trust_region_id')

                ->leftJoin('service_users2staff', function ($join) {
                    $join->on('service_users2staff.service_user_id', '=', 'service_users.id');
                    $join->on('service_users2staff.active', '=', DB::raw('1'));
                })

                ->leftJoin('staff', 'staff.id', '=', 'service_users2staff.staff_id')

                ->join('contacts', function ($join) {
                    $join->on('contacts.service_user_id', '=', 'service_users.id');
                    $join->on('contacts.contact_type_id', '=', DB::raw('1'));
                })

                ->leftJoin(DB::raw($service_user_status_now), function ($join) {
                    $join->on('TSubCS.service_user_id', '=', 'service_users.id');
                })

                ->leftJoin(DB::raw($service_user_start_date), function ($join) {
                    $join->on('TSubSD.service_user_id', '=', 'service_users.id');
                })

                ->leftJoin(DB::raw($service_user_leave_date), function ($join) {
                    $join->on('TSubLD.service_user_id', '=', 'service_users.id');
                });

        if ($params['status'] !== 'All' && $params['report_type'] === '2') {
            $service_users = $service_users
                        ->join(DB::raw($join_status), function ($join) {
                            $join->on('TSub.id', '=', 'service_users.id');
                        })
                    // The period_to and period_from coming from the selected join constant in sql_constants MUST BE IN THE SAME ORDER THAT THE BINDINGS ARE APPLIED HERE !!!!
                    ->addBinding($params['period_to'], 'select')
                    ->addBinding($params['period_from'], 'select');
        }

        if ($params['report_type'] === '1') {
            switch ($params['status']):
                        case 1:
                            $service_users = $service_users
                                ->join('status', 'status.service_user_id', '=', 'service_users.id')
                                ->join('status_types', 'status_types.id', '=', 'status.status_type_id')
                                ->where('status_types.category', 'Current')
                                ->where('status.active', 1);
            // $where .= " (status_types.category = 'Current' AND status.active = 1) ";
            break;
            case 3:
                            $service_users = $service_users
                                ->join('status', 'status.service_user_id', '=', 'service_users.id')
                                ->join('status_types', 'status_types.id', '=', 'status.status_type_id')
                                ->where('status_types.category', 'Leaver')
                                ->where('status.active', 1);
            // $where .= " (status_types.category = 'Leaver' AND status.active = 1 AND
            // 		status.status_date  >  DATE_ADD(NOW(),INTERVAL " . LEAVER_DATE_CUT_OFF . " MONTH)) ";
            // $where .= " (status_types.category = 'Leaver' AND status.active = 1) ";
            break;
            default:
                    endswitch;
        }

        if ($params['filter']) {
            switch ($params['filter'][0]):
                        case 'a':
                            $service_users = $service_users
                                ->where('trust_areas.id', substr($params['filter'], 1));
            break;
            case 't':
                            $service_users = $service_users
                                ->where('trusts.id', substr($params['filter'], 1));
            break;
            case 'p':
                            $service_users = $service_users
                                ->where('staff_id', substr($params['filter'], 1));
            break;
            endswitch;
        }

        /*================== This deals with the service grouping selections ===============>*/
        if ($params['service_name_id']) {
            $service_users = $service_users
                    ->where('services.service_name_id', '=', $params['service_name_id']);
        }

        $service_users = $service_users

                ->groupBy('service_users.id')

                ->orderByRaw($order_by)

                ->get();

        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // SiteService::dd($last_query);

        return $service_users;

        // SiteService::dd($service_users);
    }
}
