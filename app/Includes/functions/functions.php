<?php

function getQualsForGrid($db)
{
    $service_user_id = false;
    if (isset($_GET['srvusr']) && ! empty($_GET['srvusr'])) {
        $service_user_id = htmlspecialchars($_GET['srvusr']);
        if ((string) $_GET['srvUsrStatus'] === 'Current') {
            $_SESSION['service_user']['current']['id'] = $service_user_id;
        } else {
            $_SESSION['service_user']['leaver']['id'] = $service_user_id;
        }
    }

    $query = "SELECT 
                    qualifications.id as qualification_id,
                    qualifications.qualification,
                    qualifications.ess_skills,
                    DATE_FORMAT(qualifications.date_achieved,'%d/%m/%Y') AS date_achieved,
                    qualifications.nvq_level,
                    if(QMTSub.qualification_module_id IS NULL,'No','Yes') AS has_modules
                    FROM
                    qualifications
                    LEFT JOIN
                    (SELECT 
                    qualification_modules.qualification_id,
                    qualification_modules.id AS qualification_module_id
                    FROM
                    qualification_modules
                    GROUP BY qualification_modules.qualification_id
                    ) AS QMTSub ON QMTSub.qualification_id = qualifications.id
                    WHERE
                    service_user_id =:service_user_id
                    ORDER BY
                    qualifications.date_achieved DESC";

    $bind = [':service_user_id' => $service_user_id];
    $result = $db->run($query, $bind);
    $result = sanitise_array_using_htmlspecialchars($result);

    return $result;
}
