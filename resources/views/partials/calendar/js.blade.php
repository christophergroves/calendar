<script type="text/javascript">
        
    var urlBase = "{!! URL::to('') !!}";

    var editSessionDialog = false;
    var transportDetailsDialog = false
    var calendarEditSessionDialog = false;
    var monthLastClickTime = false;
    var calendar = false;
    var userId = "{{ $user_id }}";


    var allowDayClick = true;

    var session_date_drag_start = false;

    var toolTipData = { attendance: {'':'Yes', 0: 'Yes', 1: 'No' },
                        recurrance_type: { 0: 'One Off', 1: 'week', 2: 'month' },
                        session_day: {1:'Monday',2:'Tuesday',3:'Wednesday',4:'Thursday',5:'Friday',6:'Saturday',7:'Sunday'},
                    };


      
	$(document).ready(function() {

        disableSubmitFormOnEnter();

        calendar = $('#calendar');
		
		calendar.fullCalendar({
			header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay'
			},
            columnFormat: {
                week: 'ddd D/M',
            },
			firstDay: 1,
            eventStartEditable: true,
            eventDurationEditable: false,
            height: "auto",


            eventDragStart: function( calEvent ) {
                sessionDateDragStart = date2mysql(calEvent.start);
            },

            eventDrop: function(calEvent,dayDelta,minuteDelta,allDay,revertFunc) {

                    var sessionDate  = date2mysql(calEvent.start);
                    var sessionDateChanged  = true;
                    var data = new Object;
                    data._token = $('#_token').val();

                    if(calEvent.recurrance_type !== '0'){
                        openCalendarChooseActionEventDragged(calEvent,sessionDateDragStart,url,data,sessionDateChanged);
                    }else{
                        var urlSave = url + '/calendar/edit/dragged/save/' + calEvent.userId + '/' + calEvent.id + '/edit-one-off/' + sessionDate + '/' + sessionDateDragStart;
                        var calendarID = 'calendar';
                        var dialog = false;
                        postCalendarDialogform(urlSave, calendarID, dialog, data);
                    }
            },




			dayClick: function(date, jsEvent, view) {

                if(allowDayClick){
                     if(userId === 'false'){
                        openAlertDialog('Name Not Selected','<h4>Please select a name from the Name Search dropdown above right</h4>',4);
                        return false;
                    }else{
                        let request = {};
                        // request.url = url + '/api/sessions/edit/dialog/content';
                        request.url = urlBase; 
                        request.dataType = 'HTML';
                        request.type = 'GET';
                        request.data = {
                            'userId': userId,
                            'action': 'edit-new',
                            'sessionDate': date2mysql(date),
                            'activityId': null,
                            'sessionId': null,

                        };
                        openCalendarNewSessionDialog(request,date);  
                    }
                    
                }
			},

			eventClick: function(calEvent, jsEvent, view) {
                openCalendarEditChooseActionFirst(calEvent, jsEvent, view, url);
			},

			defaultDate: new Date(),

            // Get Events       
            events: function(start, end, timezone, callback) {

                $.ajax({
                    url: urlBase + '/api/sessions/content',
                    dataType: 'JSON',
                    data: data = {
                        "userId": userId,
                        "start":date2mysql(start),
                        "end": date2mysql(end),
                    },
                    success: function(returnedData) {
                        callback(returnedData.events);
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        callback(textStatus);
                    }
                });
            },


            eventMouseover: function (data, event, view) {
                calendarShowTooltip(data, event, view);
            },

            eventMouseout: function (data, event, view) {
                 $('#tooltip_event').remove();
            },
		});     
	});


    function refreshCalendar(response){
        if(response !== 'error'){
            $('#calendar').fullCalendar( 'refetchEvents' );
        }
    }

</script>