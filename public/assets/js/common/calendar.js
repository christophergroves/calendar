function calendarGetEvents(fp, srvUsrId, start, end, timezone, callback, urlLoadEvents){

    // var urlLoadEvents = url + '/calendar/content';
    $.ajax({
        url: urlLoadEvents,
        dataType: 'json',
        tryCount:0,//current retry count
        retryLimit:1,//number of retries on fail
        timeout: 22000,//time before retry on fail 
        data: {
            fp: fp,
            start: date2mysql(start),
            end: date2mysql(end),
            srvusrid: srvUsrId,
            
        },
        success: function(returnedData) {

            srvUsrId = returnedData.service_user.service_user_id;
            refreshCalendarNameSearchSelect2(returnedData);

            
            if (srvUsrId) {
                refreshServiceUserDescription(returnedData,start,end);
                callback(returnedData.events);
            }else{
                $('#service_user_name').html('Please select a name');
            }
            
            clearNameSearchSelect("#nameSearchSel",1);
        },
        error: function(xhr, textStatus, errorThrown) {
            if (textStatus == 'timeout') {//if error is 'timeout'
                this.tryCount++;
                if (this.tryCount < this.retryLimit) {
                        $.ajax(this);//try again
                        return;
                }
                //after 3 retries to get content form server alert error message
                // alert('Error: Timeout, please press submit again');
            }else{
                redirectToLogin(url);
            }
        }
    });
}



function calendarClassesGetEvents(fp,start, end, timezone, callback, urlLoadEvents){


    // var urlLoadEvents = url + '/calendar/content';
    $.ajax({
        url: urlLoadEvents,
        dataType: 'json',
        tryCount:0,//current retry count
        retryLimit:1,//number of retries on fail
        timeout: 22000,//time before retry on fail
        data: {
            fp: fp,
            start: date2mysql(start),
            end: date2mysql(end),
            activityClassId: activityClassId,
            tutorId: tutorId,
            
        },
        success: function(returnedData) {


            $tutorId = returnedData.tutor.tutor_id;


            // activityClassId = returnedData.activity_class.activity_class_id;
            refreshCalendarNameSearchSelect2(returnedData);
            
            
            if (tutorId) {
                refreshCalendarClassesDescription(returnedData,start,end);
                callback(returnedData.events);
            }else{
                $('#service_user_name').html('Please select a name');
            }
            
            clearNameSearchSelect("#nameSearchSel",1);
        },
        error: function(xhr, textStatus, errorThrown) {
            if (textStatus == 'timeout') {//if error is 'timeout'
                this.tryCount++;
                if (this.tryCount < this.retryLimit) {
                        $.ajax(this);//try again
                        return;
                }
                //after 3 retries to get content form server alert error message
                // alert('Error: Timeout, please press submit again');
            }else{
                redirectToLogin(url);
            }
        }
    });
}





function calendarEditDialogRepeatSectionSlider(slideType,dialogTitle){
    
    switch(slideType) {
        
        case '1':
           // disableEnableDatePicker('#session_date','disable');
           // $('#session_date').attr('disabled', true);
            $('.bootstrap-dialog-title').html(dialogTitle);
            $('.recurrance_monthly').hide();
            $('.recurrance-interval-text').html('weeks');
            $('.recurrance_weekly').show();
            $('.repeats-section').slideDown(150);
            break;
        case '2':
           // disableEnableDatePicker('#session_date','disable');
           // $('#session_date').attr('disabled', true);
            $('.bootstrap-dialog-title').html(dialogTitle);
            $('.recurrance_weekly').hide();
            $('.recurrance-interval-text').html('months');
            $('.recurrance_monthly').show();
            $('.repeats-section').slideDown(150);
            break;
        default:
           // disableEnableDatePicker('#session_date','enable');
            $('.bootstrap-dialog-title').html(dialogTitle);
            $('.repeats-section').slideUp(250);
    } 
}



function postCalendarDialogform(urlSave,calendarID,dialog,data){
    
    $.ajax({
            type: 'POST',
            url: urlSave,
            data: data,
        // dataType: 'json',
            tryCount:0,//current retry count
            retryLimit:1,//number of retries on fail
            timeout: 22000,//time before retry on fail
            success: function(returnedData) {
               $('#'+calendarID).fullCalendar( 'refetchEvents' );
            },
            error: function(xhr, textStatus, errorThrown) {
                if (textStatus == 'timeout') {//if error is 'timeout'
                    this.tryCount++;
                    if (this.tryCount < this.retryLimit) {
                            $.ajax(this);//try again
                            return;
                    }
                    //after 3 retries to get content form server alert error message
                    alert('Error: Timeout, please press submit again');
                    $('#content-replaceable').html("<h4>Error: Timeout, please press submit again<h4/>");
                }
                if(textStatus === 'error'){redirectToLogin(url);}
            }
    });
}


function postCalendarActivityDialogEdit(fp,calEvent,url,urlSave,calendarID,dialog,data){

    allowDayClick = false;

    $.ajax({
            type: 'POST',
            url: urlSave,
            data: data,
        // dataType: 'json',
            tryCount:0,//current retry count
            retryLimit:1,//number of retries on fail
            timeout: 22000,//time before retry on fail
            success: function(returnedData) {

                var activityId = returnedData;

                if(calEvent.id === 0){
                    openCalendarNewSessionDialog(fp,calEvent.service_user_id,calEvent.start,false,false,url,activityId);
                }else{
                    openCalendarEditSessionDialog(fp,calEvent, false, false, url);
                }
                allowDayClick = true;
                

            // $('#'+calendarID).fullCalendar( 'refetchEvents' );
            },
            error: function(xhr, textStatus, errorThrown) {
                if (textStatus == 'timeout') {//if error is 'timeout'
                    this.tryCount++;
                    if (this.tryCount < this.retryLimit) {
                            $.ajax(this);//try again
                            return;
                    }
                    allowDayClick = true;
                    //after 3 retries to get content form server alert error message
                    alert('Error: Timeout, please press submit again');
                    $('#content-replaceable').html("<h4>Error: Timeout, please press submit again<h4/>");
                }
                if(textStatus === 'error'){redirectToLogin(url);}
            }
    });
}


function postTransportEditDialogform(urlSave,url,dialog,data,sessionID,sessionDate,recurranceType,calendarID){
    
    $.ajax({
            type: 'POST',
            url: urlSave,
            data: data,
        // dataType: 'json',
            tryCount:0,//current retry count
            retryLimit:1,//number of retries on fail
            timeout: 22000,//time before retry on fail
            success: function(returnedData) {
                $('#'+calendarID).fullCalendar( 'refetchEvents' );
                // openTransportDetails(fp,sessionID, url ,sessionDate,recurranceType);
            },
            error: function(xhr, textStatus, errorThrown) {
                if (textStatus == 'timeout') {//if error is 'timeout'
                    this.tryCount++;
                    if (this.tryCount < this.retryLimit) {
                            $.ajax(this);//try again
                            return;
                    }
                    //after 3 retries to get content form server alert error message
                    alert('Error: Timeout, please press submit again');
                }
                if(textStatus === 'error'){redirectToLogin(url);}
            }
    });
}



function postCalendarSessionAttendance(urlSave,url,dialog,data){

    dialog.close();
    
    $.ajax({
            type: 'POST',
            url: urlSave,
            data: data,
            tryCount:0,//current retry count
            retryLimit:1,//number of retries on fail
            timeout: 22000,//time before retry on fail
            success: function(returnedData) {
                    $('#calendar').fullCalendar( 'refetchEvents' );
                    
            },
            error: function(xhr, textStatus, errorThrown) {
                if (textStatus == 'timeout') {//if error is 'timeout'
                    this.tryCount++;
                    if (this.tryCount < this.retryLimit) {
                            $.ajax(this);//try again
                            return;
                    }
                    //after 3 retries to get content form server alert error message
                    alert('Error: Timeout, please press submit again');
                }
                if(textStatus === 'error'){redirectToLogin(url);}
            }
    });
}


function getModalMessageContent(url){

    $.ajax({
          
            url: url,
            dataType: 'HTML',
            tryCount:0,//current retry count
            retryLimit:1,//number of retries on fail
            timeout: 22000,//time before retry on fail
            success: function(returnedData) {

               // alert(returnedData)
                return returnedData;
            },
            error: function(xhr, textStatus, errorThrown) {
                if (textStatus == 'timeout') {//if error is 'timeout'
                    this.tryCount++;
                    if (this.tryCount < this.retryLimit) {
                            $.ajax(this);//try again
                            return;
                    }
                    //after 3 retries to get content form server alert error message
                    alert('Error: Timeout, please press submit again');
                   // $('#content-replaceable').html("<h4>Error: Timeout, please press submit again<h4/>");
                }
                if(textStatus === 'error'){redirectToLogin(url);}
            }
    });
}


function calendarShowTooltip(data, event, view){

    // alert(toolTipData.session_day[1]);

    // alert(toolTipData.session_day[data.session_day]);

        // var sessionDeletedText = '';
        var recurranceText = '';
        var activityTutorViewText = '';
        var tutorOrCaseOfficerText = '';
        var towardsQualText = '';

        if(data.recurrance_type === '1' || data.recurrance_type === '2'){
            recurranceText = (data.recurrance_interval > 1) ? toolTipData.session_day[data.session_day] + ' - Every ' + data.recurrance_interval + ' ' + toolTipData.recurrance_type[data.recurrance_type] + 's' : toolTipData.session_day[data.session_day] + ' - Every ' + toolTipData.recurrance_type[data.recurrance_type];
        }else{
            recurranceText = toolTipData.recurrance_type[data.recurrance_type];
        }

        // if(data.session_deleted === '1'){
        //     sessionDeletedText = '<span class="tooltip_label">==== Session Deleted / Occurance Moved ====</span></br><div style="height:3px;"></div>';
        // }

        if(typeof data.activity_tutor_view !== "undefined"){
            activityTutorViewText = '<span class="tooltip_label">Activity: &nbsp;</span>' + data.activity_tutor_view + '</br>' 
            tutorOrCaseOfficerText = '<span class="tooltip_label">Case Officer: &nbsp;</span>' + data.case_officer 
            towardsQualText = '<span class="tooltip_label">Towards Qual: &nbsp;</span>' + data.towards_qualification + '<div style="height:5px;"></div>'
        }else{
            tutorOrCaseOfficerText = '<span class="tooltip_label">Tutor: &nbsp;</span>' + data.tutor 
           

        }

        var tooltip = '<div id="tooltip_event" style="width:auto;height:auto;position:absolute;z-index:10001;line-height: 120%;border-radius:3px;box-shadow: 1px 1px 5px #333333;">' 

            + '<div style="background: #EFB74F; padding: 5px 8px 5px 8px; font-weight:bold; font-size:12px;">' +  data.title + '</div>'
            + '<div style=" background:#F2C979; padding: 5px 8px 6px 8px; font-size:12px;">'
                // +  sessionDeletedText
                +  activityTutorViewText
                +  towardsQualText
                + '<span class="tooltip_label">Recurrance: &nbsp;</span>' + recurranceText
                + '<div style="height:5px;"></div>'
                + '<span class="tooltip_label">Dates: &nbsp;</span>' + data.session_start_date + ' - ' + data.session_finish_date + '</br>' 
                + '<span class="tooltip_label">Times: &nbsp;</span>' + data.start_time + ' - ' + data.finish_time + '</br>'
                + '<span class="tooltip_label">Hours: &nbsp;</span>' + data.hours 
                + '<div style="height:5px;"></div>'
                + '<span class="tooltip_label">Attended: &nbsp;</span>' + toolTipData.attendance[data.attendance] + '</br>' 
                + '<span class="tooltip_label">Att Notes: &nbsp;</span>' + data.attendance_notes
                + '<div style="height:5px;"></div>'

                + tutorOrCaseOfficerText

                + '<div style="height:5px;"></div>'

                + '<span class="tooltip_label">Transport: &nbsp;</span>' + data.transport_provider 
                + '<div style="height:8px;"></div>'
                
                    + '<div style=" font-size:9px; line-height: 115%;">'
                        + '<span style="font-weight:bold;">Activity updated: &nbsp;</span>' + data.updated_activity + '</br>'
                        + '<span style="font-weight:bold;">Session updated: &nbsp;</span>' + data.updated_session + '</br>'
                        + '<span style="font-weight:bold;">Attendance updated: &nbsp;</span>' + data.updated_attendance
                + '</div>'

            + '</div>'
        + '</div>';

            $("body").append(tooltip)
            $('#tooltip_event').css('z-index', 15000);
            $(this).mousemove(function (e) {
                $('#tooltip_event').css('top', e.pageY + 10);
                $('#tooltip_event').css('left', e.pageX + 20);
            });

            tooltip = null;
            recurranceText = null;
            activityTutorViewText = null;
            tutorOrCaseOfficerText = null; 
            towardsQualText = null;
}




function openCalendarEditSessionDialog(fp,calEvent, jsEvent, view, url){


    var viewType = "Name";
    if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

    var sessionDate  = date2mysql(calEvent.start);
    
    if(calEvent.recurrance_type !== '0' && !calEvent.parent_id){
        var urlLoad = url + '/calendar/edit/dialog/content/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/edit-all/' + sessionDate;
    }else{
        if(!calEvent.parent_id){
            var urlLoad = url + '/calendar/edit/dialog/content/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/edit-one-off/' + sessionDate;
        }else{
            var urlLoad = url + '/calendar/edit/dialog/content/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/edit-instance/' + sessionDate;
        }
    }

    calendarEditSessionDialog = new BootstrapDialog({
    
   // BootstrapDialog.show({
        title: '',
        message: $('<div></div>').load(urlLoad,function(response,textStatus){
                if(response === 'Name Missmatch'){location.reload();}
                if(textStatus === 'error'){redirectToLogin(url);}
                }),
        draggable: true,
        closable: false,
        onshow: function(){
            // $("body").css("overflow", "hidden");
        },
        // onshow: function(){$("body").css("overflow-y", "scroll");},
        // onhide: function(){$("body").css("overflow", "auto");},
        buttons: [{
            label: 'Save',


            // autospin: true,
            cssClass: 'btn-primary pull-left',
            action: function(dialog) {

                // correct times if not in true 24hr format (h:mm instead of hh:mm) bug in the validator
                var start_time = $('#start_time').val();
                var finish_time = $('#finish_time').val();
                start_time = start_time.split(':');
                finish_time = finish_time.split(':');
                if(start_time[0].length === 1){
                    $('#start_time').val('0' + start_time[0] + ':' + start_time[1]);
                }
                if(finish_time[0].length === 1){
                    $('#finish_time').val('0' + finish_time[0] + ':' + finish_time[1]);
                }
                
                //check form validation
                $("#calendar_edit_form").bootstrapValidator('validate');
                if(!$("#calendar_edit_form").data('bootstrapValidator').isValid()) {return false;}
                
                var data = $('#calendar_edit_form').serialize();
                var calendarID = 'calendar';
                var recurrance_type = $('#recurrance_type').val();
                var dayCount = $('input[name="recurrance_day[]"]:checked').length;
                var activity_id = $('#activity_id').val();
                var changedToInstance = '0';
                var sessionDateChanged = false;

                if(!recurrance_type){recurrance_type = calEvent.recurrance_type;}
                // Event is an instance (i.e. child of parent recurring event)
                if(recurrance_type === '0' && calEvent.parent_id){

                    var urlSave = url + '/calendar/edit/save/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/edit-instance/' + sessionDate;
                    dialog.close();
                    postCalendarDialogform(urlSave, calendarID, dialog, data);
                // Event is not an instance (i.e. not a child of recurring event)
                }else if(recurrance_type === '0' && !calEvent.parent_id){

                    // if Event was originally weekly or monthly and has been changed to instance
                    if(recurrance_type === '0' && calEvent.recurrance_type === '1'){
                        var changedToInstance = '1';
                        openCalendarEditChooseActionEvent(fp,calEvent,url,data,sessionDateChanged,dayCount,changedToInstance);
                        dialog.close();
                    }else{
                        var urlSave = url + '/calendar/edit/save/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/edit-one-off/' + sessionDate;
                        dialog.close();
                        postCalendarDialogform(urlSave, calendarID, dialog, data);
                    }
                // Event used to be 'one off' but has now changed to weekly or monthly
                }else if(calEvent.recurrance_type === '0' && (recurrance_type === '1' || recurrance_type === '2')){
                    var urlSave = url + '/calendar/edit/save/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/edit-all/' + sessionDate;
                    dialog.close();
                    postCalendarDialogform(urlSave, calendarID, dialog, data);
                }else{
                    var sessionDateChanged = false;
                    // If stuff has changed so that the "openCalendarEditChooseActionEvent()" dialog needs to be used ...
                    if(
                        calEvent.activity_id !== $('#activity_id').val() ||
                        origSessionDate !== $('#session_date').val() ||
                        calEvent.start_time !== $('#start_time').val() || 
                        calEvent.finish_time !== $('#finish_time').val() ||
                        calEvent.hours !== $('#hours').val() ||
                        calEvent.recurrance_interval !== $('#recurrance_interval').val() ||
                        calEvent.session_start_date !== $('#start_date').val() ||
                        (calEvent.recurrance_number !== $('#recurrance_number').val() && $('#ends_on_occurances:checked').length === 1) ||
                        document.querySelectorAll('.recurrance_day:checked').length > 1
                    ){
                        if(origSessionDate !== $('#session_date').val()){var sessionDateChanged = true;};  
                        openCalendarEditChooseActionEvent(fp,calEvent,url,data,sessionDateChanged,dayCount,changedToInstance);
                        dialog.close(); 
                    }else{
                        // No need to open the "openCalendarEditChooseActionEvent()" dialog because only end dates etc have changed
                        var urlSave = url + '/calendar/edit/save/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/edit-all/' + sessionDate;
                        dialog.close();
                        postCalendarDialogform(urlSave, calendarID, dialog, data);
                    }
                }
            }
        },{
            label: 'Cancel',
            cssClass: 'btn-default pull-left',
            action: function(dialog) {
                dialog.close();
            }
        }
    //    ,{
    //        label: 'End/Remove',
    //       cssClass: 'btn-default pull-right',
    //       action: function(dialog) {
    //            openCalendarEditRemoveConfirm(calEvent, jsEvent, view, url);
    //            dialog.close();
    //       }
    //   }
        ]
    });
    calendarEditSessionDialog.open();
}



function openCalendarNewSessionDialog(fp,srvUsrId,date,jsEvent,view,url,activityId){

    var viewType = "Name";
    // if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

    sessionDate = date2mysql(date);
    var urlLoad = url + '/calendar/edit/dialog/content/' + fp + '/' + viewType + '/' + srvUsrId + '/' + 0 + '/edit-new' + '/' + sessionDate + '/' + activityId;

    var formBody = $('<div id="allHtml"></div>');
    formBody.load(urlLoad,function(response,textStatus){
                    if(response === 'Name Missmatch'){location.reload();}
                    if(textStatus === 'error'){redirectToLogin(url);}
                });

    
    calendarEditSessionDialog = new BootstrapDialog({
        title: 'Create New Event',
        message: formBody, 
        // message: $('<div></div>').load(urlLoad,function(response,textStatus){
        //             if(textStatus === 'error'){redirectToLogin(url);}
        //         }),
        draggable: true,
        closable: false,


        // onshow: function(){$("body").css("overflow", "hidden");},
        // onhide: function(){$("body").css("overflow", "auto");},
        // onshow: function(){$("body").css("overflow-y", "scroll");},
         buttons: [{
            label: 'Save',
            cssClass: 'btn-primary pull-left',
            action: function(dialog) {

            // correct times if not in true 24hr format (h:mm instead of hh:mm) bug in the validator
            var start_time = $('#start_time').val();
            var finish_time = $('#finish_time').val();
            start_time = start_time.split(':');
            finish_time = finish_time.split(':');
            if(start_time[0].length === 1){
                $('#start_time').val('0' + start_time[0] + ':' + start_time[1]);
            }
            if(finish_time[0].length === 1){
                $('#finish_time').val('0' + finish_time[0] + ':' + finish_time[1]);
            }

            //check form validation
            $("#calendar_edit_form").bootstrapValidator('validate');
            if(!$("#calendar_edit_form").data('bootstrapValidator').isValid()) {return false;}
                
            var sessionDate  = date2mysql(date);
            var data = $('#calendar_edit_form').serialize();
            var calendarID = 'calendar';
            var urlSave = url + '/calendar/edit/save/' + fp + '/' + viewType + '/' + srvUsrId + '/' + 0 + '/edit-new/' + sessionDate;
            dialog.close();
            postCalendarDialogform(urlSave, calendarID, dialog, data);
            }
        },{
            label: 'Cancel',
            cssClass: 'btn-default pull-left',
            action: function(dialog) {
                dialog.close();
            }
        }]
    });
    calendarEditSessionDialog.open();
}




function openCalendarEditRemoveConfirm(fp,calEvent, jsEvent, view ,url){

    var viewType = "Name";
    if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

    var sessionDate  = date2mysql(calEvent.start);
    var urlLoad = url + '/calendar/edit/remove/confirm/message/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
    
    BootstrapDialog.show({
        title: 'End/Remove Session (Session Date: ' + mysql2dmy(sessionDate) + ')',
        draggable: true,
        // onshow: function(){$("body").css("overflow", "hidden");},
        // onhide: function(){$("body").css("overflow", "auto");},
        // onshow: function(){$("body").css("overflow-y", "scroll");},

            message: $('<div></div>').load(urlLoad,function(response,textStatus){
                    if(textStatus === 'error'){redirectToLogin(url);}
                }),
            buttons: [{
                label: 'Remove',
                cssClass: 'btn-warning pull-left',
                action: function(dialog) {
                    
                    removeAction = false;
                    // if recurrance type is 0 then 
                    if(calEvent.recurrance_type === '0'){
                        removeAction = 'delete-all';
                    }else{
                       var removeAction = $("input:radio[name ='delete_action']:checked").val();
                    }
                    // protect against user pressing delete button before delete radio button options are loaded into dialog (no radio buttons loaded if recurrance type = 0).
                    if(!removeAction){return false;}
                    var urRemove = url + '/calendar/edit/remove/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + removeAction + '/' + calEvent.id + '/' + sessionDate;
                    var calendarID = 'calendar';
                    
                    //add csrf token
                    var data = new Object;
                    data._token = $('#_token').val();
                    dialog.close();
                    postCalendarDialogform(urRemove,calendarID,dialog,data);
                }
            }, {
                label: 'Cancel',
                cssClass: 'btn-default pull-left',
                action: function(dialog) {
                    dialog.close();
                }
            }]
    });
}


function openCalendarEditChooseActionFirst(fp,calEvent, jsEvent, view, url){

    var viewType = "Name";
    if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

    calEvent.attendance_notes = !calEvent.attendance_notes ? '' : calEvent.attendance_notes;
    var sessionDate  = date2mysql(calEvent.start);

    var urlCheckForNameMissmatch = url + '/calendar/edit/check/name_missmatch/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
    $('<div></div>').load(urlCheckForNameMissmatch,function(response,textStatus){
        if(response === 'Name Missmatch'){location.reload();}
        if(textStatus === 'error'){redirectToLogin(url);}
    }),
    
     BootstrapDialog.show({
        title: 'Choose Action (Date: ' + mysql2dmy(sessionDate) + ')',
        draggable: true,
        

        message: function (dialog) {
    
            var attendance = calEvent.attendance === "" || calEvent.attendance === '0' ? 'Attended' : 'Did not attend'; 
        
            // var staffPresentChecked = calEvent.staff_present !== "" || calEvent.staff_present === '0' ? 'checked="checked"' : '';
            // var peerSupportPresentChecked = calEvent.peer_support_present !== "" || calEvent.peer_support_present === '0' ? 'checked="checked"' : '';
            
            var containerDiv = $('<div></div>');
            var form = $('<form class="cal_choose_action_first_form"></form>');
            var textArea  = $('<textarea id="attendance_notes" rows="3" placeholder="Please enter any helpful notes about attendance on this particular day and click Attended or Not Attended below">' + calEvent.attendance_notes + '</textarea><br>');
            var textAreaLabel = $('<label class="display-block" style="margin-bottom:2px;">Attendance Notes</label>');
            
            // var attendance = $('<input id="attendance" type="checkbox" disabled readonly value="attendance" name="attendance" '+ attendance +'>');

            var attendance = $('<h5>'+ attendance +'</h5>');

            // var attendanceLabel = $('<label for="attendance" class="radio-label">&nbsp;Attended</label><br>');
            
            // var staffPresent = $('<input id="staff_present" type="checkbox" value="staff_present" name="staff_present" '+ staffPresentChecked +'>');
            // var StaffPresentLabel = $('<label for="staff_present" class="radio-label">&nbsp;Staff Present</label><br>');
            // var peerSupportPresent = $('<input id="peer_support_present" type="checkbox" value="peer_support_present" name="peer_support_present" '+ peerSupportPresentChecked +'>');
            // var peerSupportPresentLabel = $('<label for="peer_support_present" class="radio-label">&nbsp;Peer Support Present</label>');
            
            // Put it in dialog data's container then you can get it easier by using dialog.getData() later.
            // dialog.setData('attendance', attendance);
            dialog.setData('attendance_notes', textArea);  
            // dialog.setData('staff_present', staffPresent);
            // dialog.setData('peer_support_present', peerSupportPresent);

            // append to dom
            containerDiv.append(form);
            form.append(textAreaLabel).append(textArea);
            // form.append(attendance).append(attendanceLabel);
            form.append(attendance);

            // form.append(staffPresent).append(StaffPresentLabel);
            // form.append(peerSupportPresent).append(peerSupportPresentLabel);

            return containerDiv;
        },
        buttons: [
        {
            icon: 'glyphicon glyphicon-remove-sign',
            label: 'Not Attended',
            cssClass: 'btn-warning pull-left',
            action: function(dialog) {

                var sessionDate  = date2mysql(calEvent.start);
                
                var data = new Object;
                data.attendance = false;
                data.attendance_notes = dialog.getData('attendance_notes').val();

                // data.staff_present = false;
                // data.peer_support_present = false;

                data._token = $('#_token').val();

                var urlSave = url + '/calendar/edit/attendance/save/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
                postCalendarSessionAttendance(urlSave,url,dialog,data);

                // dialog.close();
            }
        },{
            icon: 'glyphicon glyphicon-ok-sign',
            label: 'Attended',
            cssClass: 'btn-success pull-left',
            action: function(dialog) {

                var sessionDate  = date2mysql(calEvent.start);
                
                var data = new Object;
                data.attendance = true;
                data.attendance_notes = dialog.getData('attendance_notes').val();

                // data.staff_present = false;
                // data.peer_support_present = false;

                data._token = $('#_token').val();

                var urlSave = url + '/calendar/edit/attendance/save/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
                postCalendarSessionAttendance(urlSave,url,dialog,data);
                // dialog.close();
            }
        },{
            label: 'Edit Session',
            cssClass: 'btn-default',
            action: function(dialog) {
                    openCalendarEditSessionDialog(fp,calEvent, jsEvent, view, url);
                dialog.close();
            }
        },{
            label: 'Edit Transport',
            cssClass: 'btn-default',
            action: function(dialog) {
               var sessionDate  = date2mysql(calEvent.start);
               openTransportDetails(fp,viewType,calEvent.service_user_id,calEvent.id, url, sessionDate,calEvent.recurrance_type);
               dialog.close();
            }
        },{
            label: 'Remove Session',
            cssClass: 'btn-warning ',
            action: function(dialog) {
                openCalendarEditRemoveConfirm(fp,calEvent, jsEvent, view, url);
                dialog.close();
            }
        },{
            label: 'Cancel',
            cssClass: 'btn-default',
            action: function(dialog) {
                dialog.close();
            }
        }]
    });
}




function openCalendarEditChooseActionEvent(fp,calEvent, url, data, sessionDateChanged, dayCount, changedToInstance){

        var viewType = "Name";
        if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

        var sessionDate  = date2mysql(calEvent.start);

        var urlLoad = url + '/calendar/edit/action/dialog/content/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate + '/' + sessionDateChanged + '/' + dayCount + '/' + changedToInstance;
        BootstrapDialog.show({
        title: 'Choose Edit Action (Session Date: ' + mysql2dmy(sessionDate) + ')',
        message: $('<div></div>').load(urlLoad,function(response,textStatus){
                    if(textStatus === 'error'){redirectToLogin(url);}
                }),
        draggable: true,
        closable: false,
              buttons: [{
                label: 'Save',
                cssClass: 'btn-primary pull-left',
                action: function(dialog) {
                    var editAction = $("input:radio[name ='edit_action']:checked").val();
                    if(!editAction){return false;}
                    var urlSave = url + '/calendar/edit/save/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/' + editAction + '/' + sessionDate + '/' + changedToInstance;;
                    var calendarID = 'calendar';
                    dialog.close();
                    postCalendarDialogform(urlSave, calendarID, dialog, data);
                }
            }, {
                label: 'Cancel',
                cssClass: 'btn-default pull-left',
                action: function(dialog) {
                    dialog.close();
                }
            }]
        });
}





function openCalendarChooseActionEventDragged(fp,calEvent, sessionDateDragStart, url, data, sessionDateChanged){


        var viewType = "Name";
        if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

        var sessionDate  = date2mysql(calEvent.start);
        
        var urlLoad = url + '/calendar/edit/action/dialog/content/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate + '/' + sessionDateChanged;
        BootstrapDialog.show({
        title: 'Choose Edit Action (Session Date: ' + mysql2dmy(sessionDate) + ')',
        message: $('<div></div>').load(urlLoad,function(response,textStatus){
                    if(textStatus === 'error'){redirectToLogin(url);}
                }),
        draggable: true,
        closable: false,

              buttons: [{
                label: 'Save',
                cssClass: 'btn-primary pull-left',
                action: function(dialog) {
                    var editAction = $("input:radio[name ='edit_action']:checked").val();
                    if(!editAction){return false;}
                    var urlSave = url + '/calendar/edit/dragged/save/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/' + editAction + '/' + sessionDate + '/' + sessionDateDragStart;
                    var calendarID = 'calendar';
                    dialog.close();
                    postCalendarDialogform(urlSave, calendarID, dialog, data);
                }
            }, {
                label: 'Cancel',
                cssClass: 'btn-default pull-left',
                action: function(dialog) {
                    $('#calendar').fullCalendar( 'refetchEvents' );
                    dialog.close();
                }
            }]
        });
}



function openCalendarEditDeletedRemoveConfirm(fp,calEvent,url){

    var sessionDate  = date2mysql(calEvent.start);
    
    BootstrapDialog.show({
        title: 'Deleted Session (Session Date: ' + mysql2dmy(sessionDate) + ')',
        draggable: true,

            message: '<h4 class="text-primary" style="margin:0 10px;">Remove the deleted status of this session instance on ' + mysql2dmy(sessionDate) + ' (originally in recurring set)?</h4>',
            buttons: [{
                label: 'Undelete',
                cssClass: 'btn-warning pull-left',
                action: function(dialog) {
                   
                    var urRemove = url + '/calendar/edit/deleted/remove/' + fp + '/' + calEvent.id + '/' + sessionDate;
                    var calendarID = 'calendar';
                    
                    //add csrf token
                    var data = new Object;
                    data._token = $('#_token').val();
                    dialog.close();
                    postCalendarDialogform(urRemove,calendarID,dialog,data);
                }
            }, {
                label: 'Cancel',
                cssClass: 'btn-default pull-left',
                action: function(dialog) {
                    dialog.close();
                }
            }]
    });
}



function openCalendarActivityEdit(fp,calEvent,url){

    var viewType = "Name";
    if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

    var sessionDate  = date2mysql(calEvent.start);
    var calendarID = 'calendar';
    var urlLoad = url + '/calendar/activity/edit/dialog/content/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
    BootstrapDialog.show({
        title: 'Add Activity (Please ensure that this activity does not already exist)',
        message: $('<div></div>').load(urlLoad,function(response,textStatus){
                    if(textStatus === 'error'){redirectToLogin(url);}
                }),
        draggable: true,
        closable: false,
        buttons: [{
            label: 'Save',
            cssClass: 'btn-primary pull-left',
            action: function(dialog) {
                
                 //check form validation
                $("#calendar_activity_edit_form").bootstrapValidator('validate');
                if(!$("#calendar_activity_edit_form").data('bootstrapValidator').isValid()) {return false;}
                
                var data = $('#calendar_activity_edit_form').serialize();
               
                dialog.close();

                var urlSave = url + '/calendar/activity/edit/save/' + fp + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
                postCalendarActivityDialogEdit(fp,calEvent,url,urlSave,calendarID,dialog,data);

                    // dialog.close();
                    //openTransportEditChooseAction(url,data,transportID,sessionID,sessionDate,recurranceType,calendarID);
                
            }
        },{
            label: 'Cancel',
            cssClass: 'btn-default pull-left',
            action: function(dialog) {
               dialog.close();
               // openTransportDetails(sessionID,url,sessionDate,recurranceType);
            }
        }]
    });
}



function openTransportDetails(fp, viewType, srvUserID, sessionID, url, sessionDate, recurranceType){

    var urlLoad = url + '/transport/details/dialog/content/' + fp + '/' + viewType + '/' + srvUserID + '/' + sessionID + '/' + sessionDate;
    
    transportDetailsDialog = new BootstrapDialog({
        title: 'Transport Details (Session Date: ' + mysql2dmy(sessionDate) + ')',
        message: $('<div></div>').load(urlLoad,function(response,textStatus){
                if(textStatus === 'error'){redirectToLogin(url);}
        }),
        draggable: true,
        // onshow: function(){$("body").css("overflow", "hidden");},
        // onhide: function(){$("body").css("overflow", "auto");},
        buttons: [{
            label: 'Add Journey',
            cssClass: 'btn-default pull-left',
            action: function(dialog) {
               openTransportEdit(fp,viewType,srvUserID,0,url,sessionID,sessionDate,recurranceType);
               dialog.close();
            }
        },{
            label: 'Cancel',
            cssClass: 'btn-default pull-left',
            action: function(dialog) {
               dialog.close();
            }
        },]
    });
    transportDetailsDialog.open();
}






function openTransportEdit(fp,viewType, srvUserID,transportID,url,sessionID,sessionDate,recurranceType,isExtended){

    isExtended = (typeof(isExtended)==='undefined') ? false : isExtended ;
    
    var calendarID = 'calendar';
    var urlLoad = url + '/transport/edit/dialog/content/' + fp + '/' + viewType + '/' + srvUserID + '/' + transportID + '/' + sessionDate + '/' + isExtended;
   BootstrapDialog.show({
        title: 'Edit Transport  (Session Date: ' + mysql2dmy(sessionDate) + ')',
        message: $('<div></div>').load(urlLoad,function(response,textStatus){
                    if(textStatus === 'error'){redirectToLogin(url);}
                }),
        draggable: true,
        closable: false,
        buttons: [{
            label: 'Save',
            cssClass: 'btn-primary pull-left',
            action: function(dialog) {
                
                 //check form validation
                $("#transport_edit_form").bootstrapValidator('validate');
                if(!$("#transport_edit_form").data('bootstrapValidator').isValid()) {return false;}
                
                var data = $('#transport_edit_form').serialize();
                // var start_date = $('#start_date').val();
                // var finish_date = $('#finish_date').val();

                // var one_off = false;
                // if (start_date != '' && finish_date != '' && start_date === finish_date){
                //     one_off = true;
                // }


                if(recurranceType === "0" || transportID === 0 || isExtended){
                    dialog.close();
                    var calendarID = 'calendar';
                    var editAction = isExtended ? 'edit-dates' : 'edit-all';

                    var urlSave = url + '/transport/edit/save/' + fp + '/' + viewType + '/' + srvUserID + '/' + transportID  + '/' + editAction  + '/' + sessionID + '/' + sessionDate;
                    postTransportEditDialogform(urlSave,url,dialog,data,sessionID,sessionDate,recurranceType,calendarID);
                }else{
                    dialog.close();
                    openTransportEditChooseAction(fp,viewType,srvUserID,url,data,transportID,sessionID,sessionDate,recurranceType,calendarID);
                }
            }
        },{
            label: 'Cancel',
            cssClass: 'btn-default pull-left',
            action: function(dialog) {
               dialog.close();
               openTransportDetails(fp,viewType,srvUserID,sessionID,url,sessionDate,recurranceType);
            }
        },{
            label: 'Remove',
            cssClass: 'btn-warning',
            action: function(dialog) {
               dialog.close();
               openTransportEditRemoveConfirm(fp,viewType,srvUserID,url,transportID,sessionID,sessionDate,recurranceType);
               
            }
        }]
    });
}




function openTransportEditChooseAction(fp,viewType, srvUserID,url,data,transportID,sessionID,sessionDate,recurranceType){

        var calendarID = 'calendar';
        var urlLoad = url + '/transport/edit/action/dialog/content/' + fp + '/' + viewType + '/' + srvUserID + '/' + transportID + '/' + sessionDate;
        BootstrapDialog.show({
        title: 'Choose Edit Action (Session Date: ' + mysql2dmy(sessionDate) + ')',
        message: $('<div></div>').load(urlLoad,function(response,textStatus){
                    if(textStatus === 'error'){redirectToLogin(url);}
                }),
        draggable: true,
       
            buttons: [{
                label: 'Save',
                cssClass: 'btn-primary pull-left',
                action: function(dialog) {
                    data = data + $('#transport_edit_action_form').serialize();

                    var editAction = $("input:radio[name ='edit_action']:checked").val();
                    if(!editAction){return false;}
                    var urlSave = url + '/transport/edit/save/' + fp + '/' + viewType + '/' + srvUserID + '/' + transportID + '/' + editAction + '/' + sessionID + '/' + sessionDate;
                    postTransportEditDialogform(urlSave,url,dialog,data,sessionID,sessionDate,recurranceType,calendarID);
                    dialog.close();
                }
            }, {
                label: 'Cancel',
                cssClass: 'btn-default pull-left',
                action: function(dialog) {
                    dialog.close();
                }
            }]
        });
}




function openTransportEditRemoveConfirm(fp,viewType,srvUserID,url,transportID,sessionID,sessionDate,recurranceType){

    var urlLoad = url + '/transport/edit/remove/confirm/message/' + fp + '/' + viewType + '/' + srvUserID + '/' + transportID + '/' + recurranceType + '/' + sessionDate;
    var calendarID = 'calendar';
    
     BootstrapDialog.show({
        title: 'Remove Journey',
        draggable: true,
        // onshow: function(){$("body").css("overflow", "hidden");},
        // onhide: function(){$("body").css("overflow", "auto");},
        message: $('<div></div>').load(urlLoad,function(response,textStatus){
         if(textStatus === 'error'){redirectToLogin(url);}
        }),
            buttons: [{
                label: 'Remove',
                cssClass: 'btn-warning pull-left',
                action: function(dialog) {

                    removeAction = false;
                    // if recurrance type is 0 then 

                    if(recurranceType === '0'){
                        removeAction = 'delete-all';
                    }else{
                       var removeAction = $("input:radio[name ='delete_action']:checked").val();
                       var period = $("#period").val();
                       if(typeof removeAction === "undefined"){removeAction = 'delete-all'; period = 0;}
                    }
                    // protect against user pressing delete button before delete radio button options are loaded into dialog (no radio buttons loaded if recurrance type = 0).
                    // if(!removeAction){return false;}

                    var urlRemove = url + '/transport/edit/remove/' + fp + '/' + viewType + '/' + srvUserID + '/' + transportID + '/' + removeAction + '/' + period + '/' + sessionDate;
                    var calendarID = 'calendar';

                    //add csrf token
                    var data = new Object;
                    data._token = $('#_token').val();

                    dialog.close();
                    postTransportEditDialogform(urlRemove,url,dialog,data,sessionID,sessionDate,recurranceType,calendarID)
                }
            }, {
                label: 'Cancel',
                cssClass: 'btn-default pull-left',
                action: function(dialog) {
                    dialog.close();
                }
            }]
        });
}