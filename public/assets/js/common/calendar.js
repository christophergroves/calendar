function calendarGetEvents(userId, start, end, timezone, callback, urlLoadEvents){

    // var urlLoadEvents = url + '/calendar/content';
    $.ajax({
        url: urlLoadEvents,
        dataType: 'json',
        tryCount:0,//current retry count
        retryLimit:1,//number of retries on fail
        timeout: 22000,//time before retry on fail 
        data: {
            start: date2mysql(start),
            end: date2mysql(end),
            user_id: userId,
            
        },
        success: function(returnedData) {
            callback(returnedData.events);
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




function sendRequest(request,parseEvents,callback){

    $.ajax({
        type: request.type,
        url: request.url,
        data: request.data,
        dataType: request.dataType,

    success: function(returnedData) {
        // $('#'+calendarID).fullCalendar( 'refetchEvents' );
        // callback(returnedData,callback)
        parseEvents(returnedData,callback)
    },
    error: function(xhr, textStatus, errorThrown) {
        callback(xhr + ' ' + errorThrown);
    }
    });
};













function postCalendarDialogform(urlSave,calendarID,dialog,data,callback){
    
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
            //    callback(returnedData)
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


function postCalendarActivityDialogEdit(calEvent,url,urlSave,calendarID,dialog,data){

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
                    openCalendarNewSessionDialog(calEvent.service_user_id,calEvent.start,false,false,url,activityId);
                }else{
                    openCalendarEditSessionDialog(calEvent, false, false, url);
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
                // openTransportDetails(sessionID, url ,sessionDate,recurranceType);
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

        var recurranceText = '';
        var activityTutorViewText = '';
        var tutorOrCaseOfficerText = '';
        var towardsQualText = '';

        if(data.recurrance_type === '1' || data.recurrance_type === '2'){
            recurranceText = (data.recurrance_interval > 1) ? toolTipData.session_day[data.session_day] + ' - Every ' + data.recurrance_interval + ' ' + toolTipData.recurrance_type[data.recurrance_type] + 's' : toolTipData.session_day[data.session_day] + ' - Every ' + toolTipData.recurrance_type[data.recurrance_type];
        }else{
            recurranceText = toolTipData.recurrance_type[data.recurrance_type];
        }

        var tooltip = '<div id="tooltip_event" style="width:auto;height:auto;position:absolute;z-index:10001;line-height: 120%;border-radius:3px;box-shadow: 1px 1px 5px #333333;">' 

            + '<div style="background: #EFB74F; padding: 5px 8px 5px 8px; font-weight:bold; font-size:12px;">' +  data.title + '</div>'
                + '<div style=" background:#F2C979; padding: 5px 8px 6px 8px; font-size:12px;">'
                    + '<span class="tooltip_label">Recurrance: &nbsp;</span>' + recurranceText
                    + '<div style="height:5px;"></div>'
                    + '<span class="tooltip_label">Dates: &nbsp;</span>' + data.session_start_date + ' - ' + data.session_finish_date + '</br>' 
                    + '<span class="tooltip_label">Times: &nbsp;</span>' + data.start_time + ' - ' + data.finish_time + '</br>'
                    + '<span class="tooltip_label">Hours: &nbsp;</span>' + data.hours 
                    + '<div style="height:5px;"></div>'
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




function openCalendarEditSessionDialog(calEvent, jsEvent, view, url){

    var sessionDate  = date2mysql(calEvent.start);
    
    if(calEvent.recurrance_type !== '0' && !calEvent.parent_id){
        var urlLoad = url + '/calendar/edit/dialog/content/'  + calEvent.userId + '/' + calEvent.id + '/edit-all/' + sessionDate;
    }else{
        if(!calEvent.parent_id){
            var urlLoad = url + '/calendar/edit/dialog/content/' + calEvent.userId + '/' + calEvent.id + '/edit-one-off/' + sessionDate;
        }else{
            var urlLoad = url + '/calendar/edit/dialog/content/' + calEvent.userId + '/' + calEvent.id + '/edit-instance/' + sessionDate;
        }
    }

    calendarEditSessionDialog = new BootstrapDialog({
    
        title: '',
        message: $('<div></div>').load(urlLoad,function(response,textStatus){
                if(textStatus === 'error'){redirectToLogin(url);}
                }),
        draggable: true,
        closable: false,
        onshow: function(){
        },
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
                        openCalendarEditChooseActionEvent(calEvent,url,data,sessionDateChanged,dayCount,changedToInstance);
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
                        openCalendarEditChooseActionEvent(calEvent,url,data,sessionDateChanged,dayCount,changedToInstance);
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
        ]
    });
    calendarEditSessionDialog.open();
}


function openCalendarNewSessionDialog(request,date){

    // sessionDate = date2mysql(date);
    var urlLoad = url + '/calendar/edit/dialog/content/' + srvUsrId + '/' + 0 + '/edit-new' + '/' + sessionDate + '/' + activityId;

    var formBody = $('<div id="allHtml"></div>');

    formBody.load(request.url, function(response,textStatus){
                    if(response === 'Name Missmatch'){location.reload();}
                    if(textStatus === 'error'){redirectToLogin(url);}
                });

    
    calendarEditSessionDialog = new BootstrapDialog({
        title: 'Create New Event',
        message: formBody, 
        draggable: true,
        closable: false,

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
            var urlSave = url + '/calendar/edit/save/' + srvUsrId + '/' + 0 + '/edit-new/' + sessionDate;
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




function openCalendarEditRemoveConfirm(calEvent, jsEvent, view ,url){

    var viewType = "Name";
    if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

    var sessionDate  = date2mysql(calEvent.start);
    var urlLoad = url + '/calendar/edit/remove/confirm/message/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
    
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
                    var urRemove = url + '/calendar/edit/remove/' + calEvent.service_user_id + '/' + removeAction + '/' + calEvent.id + '/' + sessionDate;
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


function openCalendarEditChooseActionFirst(calEvent, jsEvent, view, url){

    var viewType = "Name";
    if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

    calEvent.attendance_notes = !calEvent.attendance_notes ? '' : calEvent.attendance_notes;
    var sessionDate  = date2mysql(calEvent.start);

     BootstrapDialog.show({
        title: 'Choose Action (Date: ' + mysql2dmy(sessionDate) + ')',
        draggable: true,
    
        message: function (dialog) {
    
            var attendance = calEvent.attendance === "" || calEvent.attendance === '0' ? 'Attended' : 'Did not attend'; 
            
            var containerDiv = $('<div></div>');
            var form = $('<form class="cal_choose_action_first_form"></form>');
            var textArea  = $('<textarea id="attendance_notes" rows="3" placeholder="Please enter any helpful notes about attendance on this particular day and click Attended or Not Attended below">' + calEvent.attendance_notes + '</textarea><br>');
            var textAreaLabel = $('<label class="display-block" style="margin-bottom:2px;">Attendance Notes</label>');
  
            var attendance = $('<h5>'+ attendance +'</h5>');
            
            // Put it in dialog data's container then you can get it easier by using dialog.getData() later.
            // dialog.setData('attendance', attendance);
            dialog.setData('attendance_notes', textArea);  

            // append to DOM
            containerDiv.append(form);
            form.append(textAreaLabel).append(textArea);
            form.append(attendance);

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

                data._token = $('#_token').val();

                var urlSave = url + '/calendar/edit/attendance/save/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
                postCalendarSessionAttendance(urlSave,url,dialog,data);
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
                data._token = $('#_token').val();

                var urlSave = url + '/calendar/edit/attendance/save/'  + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
                postCalendarSessionAttendance(urlSave,url,dialog,data);
                // dialog.close();
            }
        },{
            label: 'Edit Session',
            cssClass: 'btn-default',
            action: function(dialog) {
                    openCalendarEditSessionDialog(calEvent, jsEvent, view, url);
                dialog.close();
            }
        },{
            label: 'Edit Transport',
            cssClass: 'btn-default',
            action: function(dialog) {
               var sessionDate  = date2mysql(calEvent.start);
               openTransportDetails(viewType,calEvent.service_user_id,calEvent.id, url, sessionDate,calEvent.recurrance_type);
               dialog.close();
            }
        },{
            label: 'Remove Session',
            cssClass: 'btn-warning ',
            action: function(dialog) {
                openCalendarEditRemoveConfirm(calEvent, jsEvent, view, url);
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




function openCalendarEditChooseActionEvent(calEvent, url, data, sessionDateChanged, dayCount, changedToInstance){

        var viewType = "Name";
        if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

        var sessionDate  = date2mysql(calEvent.start);

        var urlLoad = url + '/calendar/edit/action/dialog/content/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate + '/' + sessionDateChanged + '/' + dayCount + '/' + changedToInstance;
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
                    var urlSave = url + '/calendar/edit/save/' + calEvent.service_user_id + '/' + calEvent.id + '/' + editAction + '/' + sessionDate + '/' + changedToInstance;;
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





function openCalendarChooseActionEventDragged(calEvent, sessionDateDragStart, url, data, sessionDateChanged){

        var viewType = "Name";
        if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

        var sessionDate  = date2mysql(calEvent.start);
        
        var urlLoad = url + '/calendar/edit/action/dialog/content/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate + '/' + sessionDateChanged;
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
                    var urlSave = url + '/calendar/edit/dragged/save/' + calEvent.service_user_id + '/' + calEvent.id + '/' + editAction + '/' + sessionDate + '/' + sessionDateDragStart;
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



function openCalendarEditDeletedRemoveConfirm(calEvent,url){

    var sessionDate  = date2mysql(calEvent.start);
    
    BootstrapDialog.show({
        title: 'Deleted Session (Session Date: ' + mysql2dmy(sessionDate) + ')',
        draggable: true,

            message: '<h4 class="text-primary" style="margin:0 10px;">Remove the deleted status of this session instance on ' + mysql2dmy(sessionDate) + ' (originally in recurring set)?</h4>',
            buttons: [{
                label: 'Undelete',
                cssClass: 'btn-warning pull-left',
                action: function(dialog) {
                   
                    var urRemove = url + '/calendar/edit/deleted/remove/' + calEvent.id + '/' + sessionDate;
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



function openCalendarActivityEdit(calEvent,url){

    var viewType = "Name";
    if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}

    var sessionDate  = date2mysql(calEvent.start);
    var calendarID = 'calendar';
    var urlLoad = url + '/calendar/activity/edit/dialog/content/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
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

                var urlSave = url + '/calendar/activity/edit/save/' + calEvent.service_user_id + '/' + calEvent.id + '/' + sessionDate;
                postCalendarActivityDialogEdit(calEvent,url,urlSave,calendarID,dialog,data);
                
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

function calendarEditDialogRepeatSectionSlider(slideType,dialogTitle){
    
    switch(slideType) {
        
        case '1':
            $('.bootstrap-dialog-title').html(dialogTitle);
            $('.recurrance_monthly').hide();
            $('.recurrance-interval-text').html('weeks');
            $('.recurrance_weekly').show();
            $('.repeats-section').slideDown(150);
            break;
        case '2':
            $('.bootstrap-dialog-title').html(dialogTitle);
            $('.recurrance_weekly').hide();
            $('.recurrance-interval-text').html('months');
            $('.recurrance_monthly').show();
            $('.repeats-section').slideDown(150);
            break;
        default:
            $('.bootstrap-dialog-title').html(dialogTitle);
            $('.repeats-section').slideUp(250);
    } 
}
