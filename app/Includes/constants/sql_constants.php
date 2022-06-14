<?php

/*============================= FILTER ON STAFF ==========================>*/
define('INNER_JOIN_FILTER_SERVICE_ON_STAFF',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id,
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	LEFT JOIN `status` AS T2 ON `status`.service_user_id = T2.service_user_id AND T2.status_type_id = 3
	LEFT JOIN `status` AS T3 ON `status`.service_user_id = T3.service_user_id AND T3.status_type_id = 4
	WHERE 
	`status`.status_type_id = 1 AND
	`status`.status_date < :period_from AND
	(T2.status_date >= :period_from OR
	T2.status_date IS NULL OR
	T2.status_date = 0) AND
	(T3.status_date >= :period_from OR
	T3.status_date IS NULL OR
	T3.status_date=0)) as TSub
	ON TSub.id = service_users.id'
);

define('INNER_JOIN_FILTER_STARTERS_DURING_THIS_PERIOD',
    ' INNER JOIN 
	(SELECT DISTINCT 
	service_users.id,
	`status`.status_date as start_date,
	TIMESTAMPDIFF(YEAR,date_of_birth,status.status_date) as AgeAtStart
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	LEFT JOIN `status` AS T2 ON `status`.service_user_id = T2.service_user_id AND T2.status_type_id = 3
	LEFT JOIN `status` as T3 on `status`.service_user_id = T3.service_user_id AND T3.status_type_id = 4
	WHERE '.
    //========== Service User start date within this period ============>
    '`status`.status_type_id = 1 AND
	`status`.status_date <= :period_to AND
	`status`.status_date >= :period_from AND
	(T2.status_date >= :period_from OR
	T2.status_date IS NULL OR
	T2.status_date = 0) AND
	(T3.status_date >= :period_from OR
	T3.status_date IS NULL OR
	T3.status_date=0)) as TSub
	ON TSub.id = service_users.id '
);

/*============================= Filter on status all  ==========================>*/
define('INNER_JOIN_FILTER_STATUS_ALL',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id,
	`status`.status_date as start_date
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	LEFT JOIN `status` AS T3 ON `status`.service_user_id = T3.service_user_id AND T3.status_type_id = 3
	LEFT JOIN `status` AS T4 ON `status`.service_user_id = T4.service_user_id AND T4.status_type_id = 4
	WHERE 
	(
	(`status`.status_type_id = 1 AND `status`.status_date < :period_from) 
	OR 
	(`status`.status_type_id = 5 AND `status`.status_date < :period_from) 
	OR 
	(`status`.status_type_id = 6 AND `status`.status_date < :period_from) 
	)
	AND
	(T3.status_date >= :period_from OR
	T3.status_date IS NULL OR
	T3.status_date = 0) AND

	(T4.status_date >= :period_from OR
	T4.status_date IS NULL OR
	T4.status_date=0)) 

	as TSub
	ON TSub.id = service_users.id '
);

/*============================= Filter on status leaver  ==========================>*/
define('INNER_JOIN_FILTER_STATUS_LEAVER',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id,
	`status`.status_date as start_date
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	WHERE 
	
	`status`.status_type_id = 3 AND `status`.status_date <= :period_to AND `status`.status_date >= :period_from

	) as TSub
	ON TSub.id = service_users.id '
);

/*============================= Filter on status starter  ==========================>*/
define('INNER_JOIN_FILTER_STATUS_STARTER',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	WHERE 
	
	`status`.status_type_id = 1 AND `status`.status_date <= :period_to AND `status`.status_date >= :period_from

	) as TSub
	ON TSub.id = service_users.id '
);

/*============================= Filter on status current  ==========================>*/
define('INNER_JOIN_FILTER_STATUS_CURRENT',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	LEFT JOIN `status` AS T3 ON `status`.service_user_id = T3.service_user_id AND T3.status_type_id = 3
	WHERE 

	`status`.status_type_id = 1 AND `status`.status_date <= :period_to
	AND
	(T3.status_date > :period_from OR
	T3.status_date IS NULL OR
	T3.status_date = 0)) 

	as TSub
	ON TSub.id = service_users.id '
);

/*============================= Filter on status completer  ==========================>*/
define('INNER_JOIN_FILTER_STATUS_COMPLETER',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	WHERE 
	
	`status`.status_type_id = 3 AND `status`.status_date <= :period_to AND `status`.status_date >= :period_from

	) as TSub
	ON TSub.id = service_users.id '
);

/*============================= Filter on status completer  ==========================>*/
define('INNER_JOIN_FILTER_STATUS_EARLY_LEAVER',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	WHERE 
	
	`status`.status_type_id = 4 AND `status`.status_date <= :period_to AND `status`.status_date >= :period_from
	
	) as TSub
	ON TSub.id = service_users.id '
);

/*============================= Filter on status referral ready to start  ==========================>*/
define('INNER_JOIN_FILTER_STATUS_REFERRAL_READY_TO_START',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	WHERE 
	
	`status`.status_type_id = 5 AND `status`.status_date <= :period_to AND `status`.status_date >= :period_from
	
	) as TSub
	ON TSub.id = service_users.id '
);

/*============================= Filter on status referral not ready to start  ==========================>*/
define('INNER_JOIN_FILTER_STATUS_REFERRAL_NOT_READY_TO_START',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	WHERE 
	
	`status`.status_type_id = 6 AND `status`.status_date <= :period_to AND `status`.status_date >= :period_from
	
	) as TSub
	ON TSub.id = service_users.id '
);

/*============================= Filter on status unsuccessful referral  ==========================>*/
define('INNER_JOIN_FILTER_STATUS_UNSUCCESSFUL_REFERRAL',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	WHERE 
	
	`status`.status_type_id = 2 AND `status`.status_date <= :period_to AND `status`.status_date >= :period_from
	
	) as TSub
	ON TSub.id = service_users.id '
);

/*=============================  ==========================>*/
// Dont mess with this! This is correct for the reports that use this! We need to exclude starters on this date! We need to include leavers on this date
// This is so that when you add the starters (excluded) and subtract the leavers (included) it adds up to INNER_JOIN_FILTER_PARTICIPANT_NUMBERS_AT_PERIOD_END
// This cannot match the period_from/period_to reports if both dates set to period start as it will INCLUDE starters NOT exclude them!
define('INNER_JOIN_FILTER_PARTICIPANT_NUMBERS_AT_PERIOD_START',
    ' INNER JOIN 
 	(SELECT DISTINCT 
	service_users.id,
	`status`.status_date as start_date
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	LEFT JOIN `status` AS T2 ON `status`.service_user_id = T2.service_user_id AND T2.status_type_id = 3
	LEFT JOIN `status` AS T3 ON `status`.service_user_id = T3.service_user_id AND T3.status_type_id = 4
	WHERE 
	`status`.status_type_id = 1 AND
	`status`.status_date < :period_from AND
	(T2.status_date >= :period_from OR
	T2.status_date IS NULL OR
	T2.status_date = 0) AND
	(T3.status_date >= :period_from OR
	T3.status_date IS NULL OR
	T3.status_date=0)) as TSub
	ON TSub.id = service_users.id '
);

// Dont mess with this! This is correct for the reports that use this! We need to include starters on this date! We need to exclude leavers on this date
// This is so that when the starters are subtracted (included) and the leavers are added (excluded) the number goes back to INNER_JOIN_FILTER_PARTICIPANT_NUMBERS_AT_PERIOD_START
// This will match the period_from/period_to reports if both dates set to period end as it will be excluding leavers anyway!
define('INNER_JOIN_FILTER_PARTICIPANT_NUMBERS_AT_PERIOD_END',
    ' INNER JOIN 
	(SELECT DISTINCT 
	service_users.id
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	LEFT JOIN `status` AS T2 ON `status`.service_user_id = T2.service_user_id AND T2.status_type_id = 3
	LEFT JOIN `status` AS T3 ON `status`.service_user_id = T3.service_user_id AND T3.status_type_id = 4
	WHERE 
	`status`.status_type_id = 1 AND
	`status`.status_date <= :period_to AND
	(T2.status_date > :period_to OR
	T2.status_date IS NULL OR
	T2.status_date = 0) AND
	(T3.status_date > :period_to OR
	T3.status_date IS NULL OR
	T3.status_date=0)) as TSub
	ON TSub.id = service_users.id '
);

// to avoid the left join filtering the main query and thus ommiting that row use WHERE t.id IS NULL OR (leftJoinTable.yourColumn = yourFilter)
define('LEFT_JOIN_GET_CALENDAR_SESSION_CHILD',
    ' LEFT JOIN 
	(SELECT
        sessions.parent_id AS parent_id_child,
        sessions.start_date,
        sessions.finish_date
        FROM 
        sessions)
        AS TSubChild ON TSubChild.parent_id_child = sessions.id AND 
        TSubChild.start_date = DATE_ADD(:week_beginning,INTERVAL sessions.session_day - 1 DAY) AND
        TSubChild.finish_date = DATE_ADD(:week_beginning,INTERVAL sessions.session_day - 1 DAY)'
);

define('INNER_JOIN_FILTER_SERVICE_USERS_BY_STAFF_SERVICES',
    ' INNER JOIN (SELECT DISTINCT
		service_users.id
		FROM 
		service_users
		INNER JOIN service_users2services ON service_users2services.service_user_id = service_users.id
		INNER JOIN staff2services ON staff2services.service_id = service_users2services.service_id
		WHERE 
		staff2services.staff_id = :staff_id) AS TSub ON TSub.id = service_users.id '
);

define('INNER_JOIN_FILTER_EARLY_LEAVERS_IN_THIS_PERIOD',
' INNER JOIN 
	(SELECT DISTINCT 
	service_users.id,
	TSubLD.leave_date
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id '.

    /*================= same as left join get start date constant ==================>*/
    ' LEFT JOIN (SELECT DISTINCT
		`status`.service_user_id,
		`status`.status_date AS start_date
		FROM 
		`status`
		WHERE 
		`status`.status_type_id = 1)
		AS TSubSD ON TSubSD.service_user_id = service_users.id '.

    /*================= same as left join get status dates (leave date part) ==================>*/
    ' LEFT JOIN (SELECT DISTINCT
		`status`.service_user_id,
		`status`.status_date AS leave_date
		FROM 
		`status`
		WHERE 
		`status`.status_type_id = 3 OR `status`.status_type_id = 4)
		AS TSubLD ON TSubLD.service_user_id = service_users.id

 	WHERE '.
    //========== Service User is Status Early Leaver within this period ============>
    '(`status`.status_type_id = 4) AND
	`status`.status_date >= :period_from AND
	`status`.status_date <= :period_to) as TSub 
	ON TSub.id = service_users.id '
);

define('INNER_JOIN_FILTER_LEAVERS_IN_THIS_PERIOD',
    ' INNER JOIN 
	(SELECT DISTINCT 
	service_users.id,
	TSubLD.leave_date
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id '.

    /*================= same as left join get start date constant ==================>*/
    ' LEFT JOIN (SELECT DISTINCT
		`status`.service_user_id,
		`status`.status_date AS start_date
		FROM 
		`status`
		WHERE 
		`status`.status_type_id = 1)
		AS TSubSD ON TSubSD.service_user_id = service_users.id '.

    /*================= same as left join get status dates (leave date part) ==================>*/
    ' LEFT JOIN (SELECT DISTINCT
		`status`.service_user_id,
		`status`.status_date AS leave_date
		FROM 
		`status`
		WHERE 
		`status`.status_type_id = 3 OR `status`.status_type_id = 4)
		AS TSubLD ON TSubLD.service_user_id = service_users.id

	WHERE '.
    //========== Service User is Status Completer or Early Leaver within this period ============>
    '(`status`.status_type_id = 3 OR `status`.status_type_id = 4) AND
	`status`.status_date >= :period_from AND
	`status`.status_date <= :period_to) as TSub 
	ON TSub.id = service_users.id '
);

define('LEFT_JOIN_QUALIFICATION_MODULES_GROUP_CONCAT',
        " LEFT JOIN 
        (SELECT 
        qualifications.service_user_id,
        qualification_modules.qualification_id,
        CONCAT('NVQ-',if(qualifications.nvq_level='0','Below 1',qualifications.nvq_level),' ',qualifications.qualification,if(qualifications.ess_skills='yes',' (Ess skills)',''),' [',module_name,']') as Module_NVQ
       
        FROM
        qualifications
        INNER JOIN qualification_modules ON qualification_modules.qualification_id = qualifications.id
        WHERE
       	(qualifications.date_achieved is not null) AND 
        qualifications.nvq_level is not null 

        ORDER BY qualifications.nvq_level ASC
        ) as qual_modules ON qual_modules.service_user_id = service_users.id "
);

define('INNER_JOIN_FILTER_CURRENT_IN_THIS_PERIOD',
     //========== Inner Join on sub query on status table so service user start and leave dates can can be checked ============>
    ' INNER JOIN 
	(SELECT DISTINCT 
	service_users.id,
	TIMESTAMPDIFF(YEAR,date_of_birth,status.status_date) as AgeAtStart
	FROM
	service_users
	INNER JOIN status ON status.service_user_id = service_users.id
	LEFT JOIN status AS T3 ON status.service_user_id = T3.service_user_id AND T3.status_type_id = 3
	WHERE '.
    //========== Service User is Status Current before period to ============>
    'status.status_type_id = 1 AND
	status.status_date <= :period_to AND '.
    //========== Service User is Status Completer after period from or has no leave date ============>
    '(T3.status_date >= :period_from OR
	T3.status_date IS NULL OR
	T3.status_date = 0)) as TSub
	ON TSub.id = service_users.id '
);

define('INNER_JOIN_FILTER_CURRENT_IN_THIS_PERIOD_OR_LEFT_WITHIN_6_MONTHS_OF_PERIOD_START',
     //========== Inner Join on sub query on status table so service user start and leave dates can can be checked ============>
    ' INNER JOIN 
	(SELECT DISTINCT 
	service_users.id,
	TIMESTAMPDIFF(YEAR,date_of_birth,status.status_date) as AgeAtStart
	FROM
	service_users
	INNER JOIN status ON status.service_user_id = service_users.id
	LEFT JOIN status AS T3 ON status.service_user_id = T3.service_user_id AND T3.status_type_id = 3
	WHERE '.
    //========== Service User is Status Current before period to ============>
    'status.status_type_id = 1 AND
	status.status_date <= :period_to AND '.
    //========== Service User is Status Completer after period from or has no leave date ============>
    '(DATE_ADD(T3.status_date, INTERVAL +6 MONTH) >= :period_from OR
	T3.status_date IS NULL OR
	T3.status_date = 0)) as TSub
	ON TSub.id = service_users.id '
);

define('INNER_JOIN_FILTER_SESSIONS_THEORY_ACCREDITED_IN_THIS_PERIOD',
    " INNER JOIN 
	(SELECT DISTINCT 
	activities.service_user_id
	FROM 
	activities
	INNER JOIN activity_types ON activity_types.id = activities.activity_type_id
	INNER JOIN sessions ON sessions.activity_id = activities.id
	WHERE 
	activity_types.type = 'Training (accredited)' AND
	sessions.start_date <= :period_to AND 
	(sessions.finish_date >= :period_from OR 
	sessions.finish_date is null OR 
	sessions.finish_date=0)) AS TSess 
	ON TSess.service_user_id = service_users.id "
);

define('INNER_JOIN_FILTER_SESSIONS_THEORY_NON_ACCREDITED_IN_THIS_PERIOD',
    " INNER JOIN 
	(SELECT DISTINCT 
	activities.service_user_id
	FROM 
	activities
	INNER JOIN activity_types ON activity_types.id = activities.activity_type_id
	INNER JOIN sessions ON sessions.activity_id = activities.id
	WHERE 
	activity_types.type = 'Training (Non-Accredited)' AND
	sessions.start_date <= :period_to AND 
	(sessions.finish_date >= :period_from OR 
	sessions.finish_date is null OR 
	sessions.finish_date=0)) AS TSess 
	ON TSess.service_user_id = service_users.id "
);

define('LEFT_JOIN_GET_SESSIONS_THEORY_ACCREDITED_IN_THIS_PERIOD',
    " LEFT JOIN 
	(SELECT DISTINCT 
	activities.service_user_id
	FROM 
	activities
	INNER JOIN activity_types ON activity_types.id = activities.activity_type_id
	INNER JOIN sessions ON sessions.activity_id = activities.id

	WHERE 

	activity_types.type = 'Training (accredited)' AND
	sessions.start_date <= :period_to AND 
	(sessions.finish_date >= :period_from OR 
	sessions.finish_date is null OR 
	sessions.finish_date=0)) AS TSess 
	ON TSess.service_user_id = service_users.id "
);

define('LEFT_JOIN_GET_SESSIONS_THEORY_ACCREDITED_QUALS_WORKING_TOWARDS_IN_THIS_PERIOD',
        " LEFT JOIN
	    (SELECT   q.service_user_id, q.nvq_level, q.qualification, q.date_achieved, q.expected_completion_date
	    FROM
	    qualifications q

	    INNER JOIN activities ON activities.qualification_id = q.id 
	    INNER JOIN activity_types ON activity_types.id = activities.activity_type_id 
		INNER JOIN sessions ON sessions.activity_id = activities.id

	    INNER JOIN 
	    (SELECT  activities.service_user_id, max(nvq_level) max_nvq_level
	    FROM
	    qualifications

	    INNER JOIN activities ON activities.qualification_id = qualifications.id
	    INNER JOIN activity_types ON activity_types.id = activities.activity_type_id
		INNER JOIN sessions ON sessions.activity_id = activities.id

	    WHERE 

	    activity_types.type = 'Training (accredited)' AND
		sessions.start_date <= :period_to AND 
		(sessions.finish_date >= :period_from OR 
		sessions.finish_date is null OR 
		sessions.finish_date=0)

	   

	    GROUP BY qualifications.service_user_id

	    ) ss ON q.service_user_id = ss.service_user_id AND q.nvq_level = ss.max_nvq_level


	    WHERE 

		activity_types.type = 'Training (accredited)' AND
		sessions.start_date <= :period_to AND 
		(sessions.finish_date >= :period_from OR 
		sessions.finish_date is null OR 
		sessions.finish_date=0)

		GROUP BY q.service_user_id
		
		
	    ) AS qual_max_working_towards ON qual_max_working_towards.service_user_id = service_users.id "
);

define('LEFT_JOIN_ESS_SKILLS_QUALIFICATIONS_GAINED_AT_ANY_TIME',
        " LEFT JOIN 
        (SELECT 
        qualifications.service_user_id,
        qualifications.id AS qualification_id,
        CONCAT_WS(' ','NVQ-',if(nvq_level='0','Below 1',nvq_level),internal_external,qualification,DATE_FORMAT( date_achieved,'%d/%m/%y')) as NVQ_Level
        FROM
        qualifications
        WHERE
        qualifications.nvq_level is not null AND qualifications.ess_skills = 'Yes'
        ORDER BY qualifications.nvq_level ASC
        ) as quals_achieved ON quals_achieved.service_user_id = service_users.id "
        );

define('INNER_JOIN_QUALIFICATIONS_COMPLETE_GAINED_IN_THIS_PERIOD_GROUP_CONCAT',
        " INNER JOIN 
        (SELECT 
        qualifications.service_user_id,
        qualifications.id AS qualification_id,
        CONCAT_WS(' ','NVQ-',if(nvq_level='0','Below 1',nvq_level),internal_external,qualification,DATE_FORMAT( date_achieved,'%d/%m/%y')) as NVQ_Level
        FROM
        qualifications
        WHERE
        (qualifications.date_achieved >= :period_from AND 
        qualifications.date_achieved <= :period_to) AND 
        qualifications.nvq_level is not null
        ORDER BY qualifications.nvq_level ASC
        ) as quals_achieved ON quals_achieved.service_user_id = service_users.id "
        );

define('INNER_JOIN_QUALIFICATION_MODULES_GAINED_IN_THIS_PERIOD_GROUP_CONCAT',
        " INNER JOIN 
        (SELECT 
        qualifications.service_user_id,
        qualification_modules.id AS qual_module_id,
        CONCAT('NVQ-',if(nvq_level='0','Below 1',nvq_level),' ',qualification, ' (', qualification_modules.module_name,' ',if(qualifications.ess_skills='yes',' (Ess skills)',''),' ', DATE_FORMAT( qualification_modules.date_achieved,'%d/%m/%y'),')') as NVQ_Level
        FROM
        qualifications
        INNER JOIN qualification_modules ON qualification_modules.qualification_id = qualifications.id
        WHERE
        (qualification_modules.date_achieved >= :period_from AND 
        qualification_modules.date_achieved <= :period_to)
        ORDER BY qualifications.nvq_level ASC
        ) as qual_modules ON qual_modules.service_user_id = service_users.id "
        );

define('LEFT_JOIN_QUALIFICATIONS_GROUP_CONCAT',
        " LEFT JOIN 
        (SELECT 
        qualifications.service_user_id,
        CONCAT_WS(' ','NVQ-',if(nvq_level='0','Below 1',nvq_level),internal_external,qualification,DATE_FORMAT( date_achieved,'%d/%m/%y')) as NVQ_Level
        FROM
        qualifications
        WHERE
        (qualifications.date_achieved is not null) AND 
        qualifications.nvq_level is not null 
        ORDER BY qualifications.nvq_level ASC
        ) as quals_achieved ON quals_achieved.service_user_id = service_users.id "
);

define('LEFT_JOIN_OUTCOMES_IMMEDIATE_GROUP_CONCAT',
        ' LEFT JOIN 
          (SELECT 
          outcomes.service_user_id,
          leaver_categories.leaver_category
          FROM 
          outcomes
          LEFT JOIN leaver_categories ON leaver_categories.id = outcomes.leaver_category_id
          WHERE outcomes.outcome_type = 1
          ORDER BY leaver_categories.list_position
          ) AS outcome_immediate ON outcome_immediate.service_user_id = service_users.id '
);

define('LEFT_JOIN_OUTCOMES_6_MONTHS_GROUP_CONCAT',
        ' LEFT JOIN 
          (SELECT 
          outcomes.service_user_id,
          leaver_categories.leaver_category
          FROM 
          outcomes
          LEFT JOIN leaver_categories ON leaver_categories.id = outcomes.leaver_category_id
          WHERE outcomes.outcome_type = 2
          ORDER BY leaver_categories.list_position
          ) AS outcome_6_months ON outcome_6_months.service_user_id = service_users.id  '
);

define('INNER_JOIN_FILTER_CURRENT_THIS_WEEK_BEGINNING',
    //========== Inner Join on sub query on status table so service user start and leave dates can can be checked ============>
    ' INNER JOIN 
	(SELECT DISTINCT 
	service_users.id,
	`status`.status_date AS startDate
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	LEFT JOIN `status` AS T2 ON `status`.service_user_id = T2.service_user_id AND T2.status_type_id = 3
	LEFT JOIN `status` AS T3 ON `status`.service_user_id = T3.service_user_id AND T3.status_type_id = 4
	WHERE '.
    //========== Service User is Status Current before week ending ============>
    '`status`.status_type_id = 1 AND
	`status`.status_date <= DATE_ADD(:week_beginning,INTERVAL 6 DAY) AND '.
    //========== Service User is Status Completer after week beginning or has no leave date ============>
    '(T2.status_date >= :week_beginning OR
	T2.status_date IS NULL OR
	T2.status_date = 0) AND '.
    //========== Service User is Status Early Leaver after week beginning or has no leave date ============>
    '(T3.status_date >= :week_beginning OR
	T3.status_date IS NULL OR
	T3.status_date=0)) as TStatus
	ON service_users.id = TStatus.id '
);

// CURRENT = 1, LEAVER = 3,

define('INNER_JOIN_FILTER_CURRENT_LEAVER_WITHIN_THIS_WEEK_BEGINNING',
    //========== Inner Join on sub query on status table so service user start and leave dates can can be checked ============>
    ' INNER JOIN 
	(SELECT DISTINCT 
	service_users.id
	FROM
	service_users
	LEFT JOIN `status` ON `status`.service_user_id = service_users.id
	LEFT JOIN `status` AS T3 ON `status`.service_user_id = T3.service_user_id AND T3.status_type_id = 3
	LEFT JOIN `status` AS T4 ON `status`.service_user_id = T4.service_user_id AND T4.status_type_id = 4

	WHERE '.
    //========== Service User is Status current before week ending ============>
    '(`status`.status_date <= :period_to AND status.status_type_id = 1) AND '.
    //========== Service User is Status Completer after week beginning or has no leave date ============>
    '(T3.status_date >= :period_from OR
	T3.status_date IS NULL OR
	T3.status_date = 0) AND '.
    //========== Service User is Status Early Leaver after week beginning or has no leave date ============>
    '(T4.status_date >= :period_from OR
	T4.status_date IS NULL OR
	T4.status_date=0)  '.
    //========== Service User is Status Early Leaver after week beginning or has no leave date ============>
    ') as TStatus
	ON service_users.id = TStatus.id '
);

define('INNER_JOIN_FILTER_CURRENT_CALENDAR',
    //========== Inner Join on sub query on status table so service user start and leave dates can can be checked ============>
    ' INNER JOIN 
	(SELECT DISTINCT 
    service_users.id,
	`status`.status_date AS startDate
	FROM
	service_users
	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	LEFT JOIN `status` AS T2 ON `status`.service_user_id = T2.service_user_id AND T2.status_type_id = 3
	LEFT JOIN `status` AS T3 ON `status`.service_user_id = T3.service_user_id AND T3.status_type_id = 4
	WHERE '.
    //========== Service User is Status Current before week ending ============>
    '`status`.status_type_id = 1 AND
	`status`.status_date <= DATE_ADD(:week_beginning,INTERVAL 6 DAY) AND '.
    //========== Service User is Status Completer after week beginning or has no leave date ============>
    '(T2.status_date >= :week_beginning OR
	T2.status_date IS NULL OR
	T2.status_date = 0) AND '.
    //========== Service User is Status Early Leaver after week beginning or has no leave date ============>
    '(T3.status_date >= :week_beginning OR
	T3.status_date IS NULL OR
	T3.status_date=0)) as TStatus
	ON service_users.id = TStatus.id '
);

define('LEFT_JOIN_FILTER_SESSIONS_THIS_WEEK_BEGINNING',

    //========== Get Session fields ============>
    "LEFT JOIN (
    SELECT 
    attendance.absence AS absence,
    CONCAT_WS(':',attendance_reasons.absence_reason,attendance.attendance_notes) AS absence_reason_concat,
    sessions.hours as sessHours,
    sessions.session_day as sessDay,
    sessions.recurrance_type,
    sessions.recurrance_interval,
    sessions.recurrance_monthly_interval,
    sessions.start_date,
    sessions.finish_date,			
    DATE_ADD(:week_beginning, INTERVAL sessions.session_day - 1 DAY) as session_date,	
    sessions.start_time as sessStartTime,
    sessions.finish_time as sessFinishTime,
    sessions.id AS sessID,
    sessions.activity_id as sessActID
    FROM
    sessions ".

    //========== Get attendance ============>
    'LEFT JOIN attendance ON attendance.session_id = sessions.id AND absence_date = DATE_ADD(:week_beginning,INTERVAL sessions.session_day - 1 DAY)
    LEFT JOIN attendance_reasons ON attendance_reasons.id = attendance.absence_reason_id '.

    //========== Activity session start and finish date filter ============>
    'WHERE 
    (attendance.session_deleted IS NULL OR attendance.session_deleted = 0) AND 
    sessions.start_date <= DATE_ADD(:week_beginning,INTERVAL 6 DAY) AND 
    (sessions.finish_date >= :week_beginning
    OR sessions.finish_date IS NULL
    OR sessions.finish_date = 0))
    AS TSess ON activities.id = TSess.sessActID '
);

// define("LEFT_JOIN_GET_SESSION_MIN_START_DATE",
//         " LEFT JOIN
//         (SELECT
//         sessions.activity_id,
//         MIN(sessions.start_date) AS start_date
//         FROM
//         sessions
//         GROUP BY
//         sessions.activity_id)
// 		AS TSessSD ON TSessSD.activity_id = activities.id "
// );

// define("LEFT_JOIN_GET_SESSION_MAX_FINISH_DATE",
//         "LEFT JOIN
//         (SELECT
//         sessions.activity_id,
//         MAX(sessions.finish_date) AS finish_date
//         FROM
//         sessions
//         GROUP BY
//         sessions.activity_id)
// 		AS TSessFD ON TSessFD.activity_id = activities.id "
// );

// define("LEFT_JOIN_GET_SESSION_MAX_START_DATE",
//         " LEFT JOIN
//         (SELECT
//         sessions.activity_id,
//         MAX(sessions.start_date) AS max_start_date
//         FROM
//         sessions
//         GROUP BY
//         sessions.activity_id)
// 		AS TSessMaxSD ON TSessMaxSD.activity_id = activities.id "
// );

//do not limit 1 or this will not work
define('LEFT_JOIN_GET_CURRENT_STATUS',
    ' LEFT JOIN (SELECT DISTINCT
		status.service_user_id,
		status_types.status_type
		FROM 
		status
		LEFT JOIN status_types ON status_types.id = status.status_type_id
		WHERE 
		status.active = 1)
		AS TSubCS ON TSubCS.service_user_id = service_users.id  '
);

// define("LEFT_JOIN_GET_SESSION_MIN_START_DATE",
//         " LEFT JOIN (SELECT
//         sessions.activity_id,
//         MIN(sessions.start_date) AS start_date
//         FROM
//         sessions
//         GROUP BY
//         sessions.activity_id) AS TSessSD ON TSessSD.activity_id = activities.id "
// );

// define("LEFT_JOIN_GET_SESSION_MAX_FINISH_DATE",
//         " LEFT JOIN (SELECT
//         sessions.activity_id,
//         IF(COUNT(*)=COUNT(sessions.finish_date),MAX(sessions.finish_date),NULL) AS finish_date
//         FROM
//         sessions

//         GROUP BY
//         sessions.activity_id) AS TSessFD ON TSessFD.activity_id = activities.id "
// );

define('LEFT_JOIN_GET_SESSION_MIN_START_DATE',
        ' LEFT JOIN (SELECT 
        sessions.activity_id,
        MIN(sessions.start_date) AS start_date
        FROM
        sessions
        LEFT JOIN attendance ON attendance.session_id = sessions.id
        WHERE 
       ((attendance.absence_date = sessions.start_date AND (attendance.session_deleted is null OR attendance.session_deleted = 0)) OR 
       	attendance.id is null OR attendance.absence_date != sessions.start_date)
        

        GROUP BY
        sessions.activity_id) AS TSessSD ON TSessSD.activity_id = activities.id '
);

define('LEFT_JOIN_GET_SESSION_MAX_FINISH_DATE',
        ' LEFT JOIN (SELECT 
        sessions.activity_id,
        IF(COUNT(*)=COUNT(sessions.finish_date),MAX(sessions.finish_date),NULL) AS finish_date
        FROM
        sessions
        LEFT JOIN attendance ON attendance.session_id = sessions.id
        WHERE
        ((attendance.absence_date = sessions.finish_date AND (attendance.session_deleted is null OR attendance.session_deleted = 0)) OR 
       	attendance.id is null OR attendance.absence_date != sessions.finish_date OR sessions.finish_date IS NULL)

        GROUP BY
        sessions.activity_id) AS TSessFD ON TSessFD.activity_id = activities.id '
);

define('INNER_JOIN_COUNT_SERVICE_USER_SERVICE',
        ' LEFT JOIN (SELECT COUNT(id) AS count,
		service_users2services.service_user_id
		FROM 
		service_users2services
		GROUP BY service_users2services.service_user_id)
		AS TSubCountServiceUserServices ON TSubCountServiceUserServices.service_user_id = service_users.id '
);

//do not limit 1 or this will not work
define('LEFT_JOIN_GET_START_DATE',
    ' LEFT JOIN (SELECT DISTINCT
		status.service_user_id,
		status.status_date AS start_date
		FROM 
		status
		WHERE 
		status.status_type_id IN (1))
		AS TSubSD ON TSubSD.service_user_id = service_users.id  '
);

//do not limit 1 or this will not work
define('LEFT_JOIN_GET_LEAVE_DATE',
    ' LEFT JOIN (SELECT DISTINCT
		status.service_user_id,
		status.status_date AS leave_date
		FROM 
		status
		WHERE 
		status.status_type_id IN (3,4))
		AS TSubLD ON TSubLD.service_user_id = service_users.id  '
);

//do not limit 1 or this will not work
define('LEFT_JOIN_GET_MIN_START_DATE',
    ' LEFT JOIN (SELECT DISTINCT
		status.service_user_service_id,
		MIN(status.status_date) AS start_date
		FROM 
		status
		WHERE 
		status.status_type_id IN (1)
        GROUP BY service_user_service_id)
		AS TSubSD ON TSubSD.service_user_service_id = service_user_service.id  '
);

//do not limit 1 or this will not work
define('LEFT_JOIN_GET_MAX_REGISTERED_DATE',
    ' LEFT JOIN (SELECT DISTINCT
		status.service_user_service_id,
		MAX(status.status_date) AS registered_date
		FROM 
		status
		WHERE 
		status.status_type_id = 1
        GROUP BY service_user_service_id)
		AS TSubRD ON TSubRD.service_user_service_id = service_user_service.id  '
);

//do not limit 1 or this will not work
define('LEFT_JOIN_GET_MAX_LEAVE_DATE',
    ' LEFT JOIN (SELECT DISTINCT
		status.service_user_service_id,
		MAX(status.status_date) AS leave_date
		FROM 
		status
		WHERE 
		status.status_type_id IN (3,4)
        GROUP BY service_user_service_id)
		AS TSubLD ON TSubLD.service_user_service_id = service_user_service.id  '
);

/*********************************************************************************/

/*----------sql Constants------------->*/

define('LEFT_JOIN_STAFF_ACCESS_TO_THIS_SERVICE_USER',
    ' LEFT JOIN 
		(SELECT DISTINCT
		staff2services.service_id
		FROM 
		staff2services
		WHERE staff2services.staff_id = :staff_id
		) AS TSubStaffAccess ON TSubStaffAccess.service_id = service_users2services.service_id '
    );

define('INNER_JOIN_STAFF_ACCESS_TO_THIS_SERVICE_USER',
    ' INNER JOIN 
		(SELECT DISTINCT
		staff2services.service_id
		FROM 
		staff2services
		WHERE staff2services.staff_id = :staff_id
		) AS TSubStaffAccess ON TSubStaffAccess.service_id = service_users2services.service_id '
    );

// $service = DB::table('services')->select('services.id','service_areas.service_area','service_names.service_name')
// 			->join('service_names','service_names.id','=','services.service_name_id')
// 			->join('service_areas','service_areas.id','=','services.service_area_id')
// 			->join('staff2services','staff2services.service_id','=','services.id')
// 			->where('staff2services.staff_id',$staff->id)
// 			->where('staff2services.service_id',$service_id)
// 			->first();

define('LEFT_JOIN_GET_CURRENT_PROJECT_OFFICER_ELOQUENT',
    " LEFT JOIN (SELECT DISTINCT
		service_user_service2staff.service_user_service_id,
		service_user_service2staff.staff_id AS project_officer_id,
		CONCAT_WS(' ',staff.firstname,staff.surname) AS project_officer,
		DATE_FORMAT(service_user_service2staff.date_start, '%d/%m/%Y') AS officer_date_start
		FROM 
		staff
		INNER JOIN service_user_service2staff ON service_user_service2staff.staff_id = staff.id
		INNER JOIN service_user_service ON service_user_service.id = service_user_service2staff.service_user_service_id
		WHERE 
		service_user_service2staff.active = 1) AS TSubPrjOfcr "
    );

define('LEFT_JOIN_GET_CURRENT_PROJECT_OFFICER',
    " LEFT JOIN (SELECT DISTINCT
		service_users2staff.service_user_id,
		service_users2staff.staff_id AS project_officer_id,
		CONCAT_WS(' ',staff.firstname,staff.surname) AS project_officer,
		service_users2staff.date_start AS officer_date_start
		FROM 
		staff
		INNER JOIN service_users2staff ON service_users2staff.staff_id = staff.id
		WHERE 
		service_users2staff.active = 1) AS TSubPrjOfcr ON TSubPrjOfcr.service_user_id = service_users.id "
    );

//		$status = DB::table('status')->whereService_user_service_id($service_user->service_user_service_id)->orderBy('status_date','DESC')->first();

define('INNER_JOIN_GET_SERVICE_USER_STATUS',
    ' INNER JOIN (SELECT 
		status.service_user_service_id,
		status.status_type_id,
		status_types.status_type
		FROM 
		status
		INNER JOIN status_types ON status_types.id = status.status_type_id
		WHERE 
		status.active = 1)
		AS TSubCurrentStatus ON TSubCurrentStatus.service_user_service_id = service_user_service.id '
    );

define('INNER_JOIN_GET_MAX_SERVICE_USER_STATUS_BY_DATE',
    ' INNER JOIN (SELECT 
		MAX(status.status_date) AS status_date,
		status.service_user_service_id,
		status.status_type_id,
		status_types.status_type
		FROM 
		status
		INNER JOIN status_types ON status_types.id = status.status_type_id
		WHERE 
		status.status_date <= :period_to
		GROUP BY service_user_service_id
		)
		AS TSubMaxStatus ON TSubMaxStatus.service_user_service_id = service_user_service.id '
    );

//do not limit 1 or this will not work
define('LEFT_JOIN_GET_STATUS_DATES',
    ' LEFT JOIN (SELECT DISTINCT
		`status`.service_user_service_id,
		`status`.status_date AS start_date
		FROM 
		`status`
		WHERE 
		`status`.status_type_id = 1)
		AS TSubSD ON TSubSD.service_user_service_id = service_user_service.id  
	LEFT JOIN (SELECT DISTINCT
		`status`.service_user_service_id,
		`status`.status_date AS referral_date
		FROM 
		`status`
		WHERE 
		`status`.status_type_id = 2)
		AS TSubRD ON TSubRD.service_user_service_id = service_user_service.id 
	LEFT JOIN (SELECT DISTINCT
		`status`.service_user_service_id,
		`status`.status_date AS waiting_list_date
		FROM 
		`status`
		WHERE 
		`status`.status_type_id = 5)
		AS TSubWD ON TSubWD.service_user_service_id = service_user_service.id 
	LEFT JOIN (SELECT DISTINCT
		`status`.service_user_service_id,
		`status`.status_date AS leave_date
		FROM 
		`status`
		WHERE 
		`status`.status_type_id = 3 OR `status`.status_type_id = 4)
		AS TSubLD ON TSubLD.service_user_service_id = service_user_service.id '
);

//do not limit 1 or this will not work
define('LEFT_JOIN_GET_STATUS_DATES_CURRENT_LEAVER',
    ' LEFT JOIN (SELECT DISTINCT
		`status`.service_user_service_id,
		`status`.status_date AS start_date
		FROM 
		`status`
		WHERE 
		`status`.status_type_id = 1)
		AS TSubSD ON TSubSD.service_user_service_id = service_user_service.id  
	LEFT JOIN (SELECT DISTINCT
		`status`.service_user_service_id,
		`status`.status_date AS leave_date
		FROM 
		`status`
		WHERE 
		`status`.status_type_id = 3 OR `status`.status_type_id = 4)
		AS TSubLD ON TSubLD.service_user_service_id = service_user_service.id '
);

define('FIELDS_SERVICE_USERS',
     " service_users.id,
	service_users.reference_no,
	service_users.other_staff,
	service_users.trust_area_id, 
	trusts.trust_name,
	trust_regions.trust_region_name,

	service_users.reference_no,
	service_users.referrer_name,
	service_users.referrer_job_title,
	service_users.referrer_tel,
	service_users.referrer_org_id,
	service_users.other_services,
	service_users.leaver_category_id,
	service_users.leaver_category_6_months_id,

	service_users.ess_skills_goal,
	service_users.ess_skills_goal_leaver,


	service_users.leaving_package,
	service_users.leaver_outcome_still_in_place_6_months,
	service_users.rickter_goals_number,
	service_users.gender,
	service_users.date_of_birth,
	service_users.transport_mode,
	service_users.disability_category,
        
	CASE
	WHEN (service_users.gender = 'M') THEN 'Male'
	WHEN (service_users.gender = 'F') THEN 'Female'
	WHEN (service_users.gender = 'SD') THEN 'Prefer to Self-Describe'
	ELSE null
	END AS gender_full, "
);

define('FIELDS_CONTACTS',
     " contacts.address_1,
	contacts.address_2,
	contacts.town,
	contacts.postcode,
	contacts.phone_1,
	contacts.phone_2,
	contacts.email,
	contacts.preferred_communication_method,
	contacts.firstname,
	contacts.surname,
	CONCAT (contacts.firstname,' ',contacts.surname) AS name, "
);

define('FIELDS_CONFIDENTIAL',
    ' confidential.nat_ins_no,
	confidential.unique_learner_no,
	confidential.unique_learner_no_privacy_notice,
	confidential.returner_service_user_id,
	confidential.disability_1,
	confidential.disability_2,
	confidential.disability_3,
	confidential.disability_other,
	confidential.gender_description, 
	confidential.economic_status_at_start,
	confidential.hours_of_paid_work_on_entry,
	confidential.hours_of_paid_work_on_leaving, '
);

define('FIELDS_STATUS_DATES',
        ' TSubSD.start_date,
		TSubRD.referral_date,
		TSubWD.waiting_list_date,
		TSubLD.leave_date,
		TIMESTAMPDIFF(YEAR,date_of_birth,TSubSD.start_date) as AgeAtStart '
);

define('FIELDS_CURRENT_PROJECT_OFFICER',
        ' TSubPrjOfcr.project_officer_id,
		TSubPrjOfcr.project_officer,
		TSubPrjOfcr.officer_date_start, '
);

// BEWARE THIS NEEDS SERVICE_USERS2SERVICES TO BE TIED TO A SERVICE (EITHER A SERVICE_ID OR SERVICE_AREA_ID !!!

define('INNER_JOIN_FILTER_LEAVERS_IN_THIS_PERIOD_WITH_STATUS',
    ' INNER JOIN 
	(SELECT DISTINCT 
	service_users.id,
	status_types.status_type AS status,
	TIMESTAMPDIFF(YEAR,date_of_birth,TSubSD.start_date) as AgeAtStart
	FROM
	service_users
	INNER JOIN service_users2services ON service_users2services.service_user_id = service_users.id AND service_users2services.active = 1

	INNER JOIN `status` ON `status`.service_user_id = service_users.id
	INNER JOIN status_types ON status_types.id = `status`.status_type_id '.
    LEFT_JOIN_GET_START_DATE.
    'WHERE '.
    //========== Service User is Status Completer or Early Leaver within this period ============>
    'service_users2services.service_id = :service_id AND 
	(`status`.status_type_id = 3 OR `status`.status_type_id = 4) AND
	`status`.status_date >= :period_from AND
	`status`.status_date <= :period_to) as TSub 
	ON service_users.id = TSub.id '
);

define('LEAVER_DATE_CUT_OFF', -48);

define('INNER_JOIN_FILTER_SESSIONS_THEORY_IN_THIS_PERIOD',
    " INNER JOIN 
    (SELECT DISTINCT 
    activities.service_user_service_id
    FROM 
    activities
    INNER JOIN activity_types ON activity_types.id = activities.activity_type_id
    INNER JOIN sessions ON sessions.activity_id = activities.id
    WHERE 
    activity_types.category = 'Theory' AND
    sessions.start_date <= :period_to AND 
    (sessions.finish_date >= :period_from OR 
    sessions.finish_date is null OR 
    sessions.finish_date=0)) AS TSess 
    ON TSess.service_user_service_id = service_user_service.id "
);

define('INNER_JOIN_EVER_SESSIONS_THEORY_ACCREDITED',
    " INNER JOIN 
    (SELECT DISTINCT 
    activities.service_user_service_id
    FROM 
    activities
    INNER JOIN activity_types ON activity_types.id = activities.activity_type_id
    INNER JOIN sessions ON sessions.activity_id = activities.id
    WHERE 
   activity_types.grouping = 'Training (accredited)') AS TSess 
    ON TSess.service_user_service_id = service_user_service.id "
);

define('LEFT_JOIN_GET_ACTIVE_PROJECT_OFFICER_NAME', //MAYBE NO NEED FOR THIS AS I JUST USED JOINS IN OCCUPANCY TO SORT THIS ISSUE OUT
    " LEFT JOIN (SELECT DISTINCT
		service_users2staff.service_user_id,
		CONCAT(staff.firstname,' ',staff.surname) AS project_officer
		FROM 
		staff
		INNER JOIN service_users2staff ON service_users2staff.staff_id = staff.id
		INNER JOIN staff2services ON staff2services.staff_id = staff.id
		WHERE service_users2staff.active = 1 AND 
		staff2services.service_id = :service_id
		) AS TSubStaff ON TSubStaff.service_user_id = service_users.id AND TSubStaff.service_id = service_user_service.service_id "
);

define('LEFT_JOIN_GET_ACTIVE_KEYWORKER_NAME2', //NO CURRENT NEED FOR THIS (USES THE service_users2services_2_staff table which has been removed)
    " LEFT JOIN (SELECT DISTINCT
		service_users2services_2_staff.service_user_service_id,
		CONCAT(staff.firstname,' ',staff.surname) AS keyworker
		FROM 
		staff
		INNER JOIN service_users2services_2_staff ON service_users2services_2_staff.staff_id = staff.id
		WHERE service_users2services_2_staff.active = 1
		) AS TSubStaff ON TSubStaff.service_user_service_id = service_user_service.id "
);

define('INNER_JOIN_FILTER_SERVICE_USERS_BY_LOGGED_IN_STAFF_SERVICES',
    ' INNER JOIN (SELECT DISTINCT
		service_users.id
		FROM 
		service_users
		INNER JOIN service_user_service ON service_user_service.service_user_id = service_users.id
		INNER JOIN staff2services ON staff2services.service_id = service_user_service.service_id
		WHERE 
		staff2services.staff_id = :staff_id) AS TSub ON TSub.id = service_users.id '
);

define('LEFT_JOIN_GET_FOLLOW_UP_1',
    " LEFT JOIN (SELECT
		follow_ups.service_user_id,
		CONCAT_WS(': ',DATE_FORMAT(follow_ups.update_date, '%d/%m/%Y'),leaver_categories.leaver_category,follow_ups.comments) AS followUp1
		FROM
		follow_ups
		LEFT JOIN leaver_categories ON leaver_categories.id = follow_ups.leaver_category_id
		WHERE 
		follow_ups.follow_up_period_id = 1) AS TSubF1 ON TSubF1.service_user_id = service_users.id "
);

define('LEFT_JOIN_GET_FOLLOW_UP_2',
    " LEFT JOIN (SELECT
		follow_ups.service_user_id,
		CONCAT_WS(': ',DATE_FORMAT(follow_ups.update_date, '%d/%m/%Y'),leaver_categories.leaver_category,follow_ups.comments) AS followUp2
		FROM
		follow_ups
		LEFT JOIN leaver_categories ON leaver_categories.id = follow_ups.leaver_category_id
		WHERE 
		follow_ups.follow_up_period_id = 2) AS TSubF2 ON TSubF2.service_user_id = service_users.id "
);

define('LEFT_JOIN_GET_FOLLOW_UP_3',
    " LEFT JOIN (SELECT
		follow_ups.service_user_id,
		CONCAT_WS(': ',DATE_FORMAT(follow_ups.update_date, '%d/%m/%Y'),leaver_categories.leaver_category,follow_ups.comments) AS followUp3
		FROM
		follow_ups
		LEFT JOIN leaver_categories ON leaver_categories.id = follow_ups.leaver_category_id
		WHERE 
		follow_ups.follow_up_period_id = 3) AS TSubF3 ON TSubF3.service_user_id = service_users.id "
);

// define("LEFT_JOIN_GET_SESSION_MIN_START_DATE",
//         " LEFT JOIN (SELECT
//         sessions.activity_id,
//         MIN(sessions.start_date) AS start_date
//         FROM
//         sessions
//         GROUP BY
//         sessions.activity_id) AS TSessSD ON TSessSD.activity_id = activities.id "
//         );

// define("LEFT_JOIN_GET_SESSION_MAX_FINISH_DATE",
//         " LEFT JOIN (SELECT
//         sessions.activity_id,
//         IF(COUNT(*)=COUNT(sessions.finish_date),MAX(sessions.finish_date),NULL) AS finish_date
//         FROM
//         sessions
//         GROUP BY
//         sessions.activity_id) AS TSessFD ON TSessFD.activity_id = activities.id "
//         );

define('LEFT_JOIN_TIMETABLE_TRANSPORT_GROUP_CONCAT',
        " LEFT JOIN 
        (SELECT
        transports.session_id,
        GROUP_CONCAT(transport_providers.short_name SEPARATOR ', ') as transport_provider
        FROM
        transports
        INNER JOIN transport_providers ON transports.transport_provider_id = transport_providers.id
        GROUP BY session_id)
        AS TPSub ON TPSub.session_id = sessions.id "
        );

define('LEFT_JOIN_QUALS_UPDATED_IN_THIS_PERIOD',
        ' LEFT JOIN 
        (SELECT
        id as quals_id,
        service_user_id
        FROM
        qualifications
        WHERE 
        updated_at <= :period_to AND 
        updated_at >= :period_from)
        AS TSubQualsUpdated ON TSubQualsUpdated.service_user_id = service_users.id '
        );

define('LEFT_JOIN_QUAL_MODULES_UPDATED_IN_THIS_PERIOD',
        ' LEFT JOIN 
        (SELECT
        qualification_modules.id as qual_module_id,
        qualifications.service_user_id
        FROM
        qualifications
        INNER JOIN qualification_modules ON qualification_modules.qualification_id = qualifications.id
        WHERE 
        qualification_modules.updated_at <= :period_to AND 
        qualification_modules.updated_at >= :period_from)
        AS TSubQualModulesUpdated ON TSubQualModulesUpdated.service_user_id = service_users.id '
        );

define('LEFT_JOIN_SESSIONS_THEORY_ACCREDITED_UPDATED_IN_THIS_PERIOD',
    " LEFT JOIN 
	(SELECT DISTINCT 
	sessions.id as sessions_id,
	activities.service_user_id
	FROM 
	activities
	INNER JOIN activity_types ON activity_types.id = activities.activity_type_id
	INNER JOIN sessions ON sessions.activity_id = activities.id
	WHERE 
	activity_types.type = 'Training (accredited)' AND
	(sessions.updated_at <= :period_to AND 
	sessions.updated_at >= :period_from) OR 
	(activities.updated_at <= :period_to AND 
	activities.updated_at >= :period_from))
	AS TSubSessionsAccreditedUpdated ON TSubSessionsAccreditedUpdated.service_user_id = service_users.id "
);
