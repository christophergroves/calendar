        
<script type="text/javascript">
        
    var url = "{!! URL::to('') !!}";

    var editSessionDialog = false;
    var transportDetailsDialog = false
    var calendarEditSessionDialog = false;
    var monthLastClickTime = false;
    var calendar = false;
    var srvusr_names = false;
    var nextRec = false;
    var prevRec = false;
    var srvUsrId = "{{ $service_user_id }}";


    var allowDayClick = true;



    var session_date_drag_start = false;

    var toolTipData = { attendance: {'':'Yes', 0: 'Yes', 1: 'No' },
                        recurrance_type: { 0: 'One Off', 1: 'week', 2: 'month' },
                        session_day: {1:'Monday',2:'Tuesday',3:'Wednesday',4:'Thursday',5:'Friday',6:'Saturday',7:'Sunday'},
                        staff_present: {'':'No', 1: 'Yes' },
                        peer_support_present: {'':'No', 1: 'Yes' },
                    };


      
	$(document).ready(function() {
            
		// initDatePicker('#start_date');
		// initDatePicker('#finish_date');
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
            // contentHeight: 800,
            eventStartEditable: true,
            eventDurationEditable: false,
            height: "auto",
            // contentHeight: 600,
            // height: 1200,

             // contentHeight: 1200,


            eventDragStart: function( calEvent ) {

                sessionDateDragStart = date2mysql(calEvent.start);

            },

            eventDrop: function(calEvent,dayDelta,minuteDelta,allDay,revertFunc) {


                    var sessionDate  = date2mysql(calEvent.start);
                    var sessionDateChanged  = true;
                    var data = new Object;
                    data._token = $('#_token').val();

                    if(calEvent.recurrance_type !== '0'){
                        openCalendarChooseActionEventDragged("{!! $fp !!}",calEvent,sessionDateDragStart,url,data,sessionDateChanged);
                    }else{
                        var viewType = "Name";
                        if (typeof(calEvent.activity_tutor_view) != "undefined"){viewType = "Tutor";}
                        var urlSave = url + '/calendar/edit/dragged/save/' + "{!! $fp !!}" + '/' + viewType + '/' + calEvent.service_user_id + '/' + calEvent.id + '/edit-one-off/' + sessionDate + '/' + sessionDateDragStart;
                        var calendarID = 'calendar';
                        var dialog = false;
                        postCalendarDialogform(urlSave, calendarID, dialog, data);
                    }
            },




			dayClick: function(date, jsEvent, view) {

                if(allowDayClick){
                     if(srvUsrId === 'false'){
                        openAlertDialog('Name Not Selected','<h4>Please select a name from the Name Search dropdown above right</h4>',4);
                        return false;
                    }else{
                        openCalendarNewSessionDialog("{!! $fp !!}",srvUsrId,date,jsEvent,view,url,0);  
                    }
                    
                }
			},

			eventClick: function(calEvent, jsEvent, view) {

                if(allowDayClick){
                    openCalendarEditChooseActionFirst("{!! $fp !!}",calEvent, jsEvent, view, url);
                }
                
			},

			defaultDate: new Date(),

             // Get Events        
            events: function(start, end, timezone, callback) {
                var urlLoadEvents = url + '/calendar/content';
                calendarGetEvents("{!! $fp !!}",srvUsrId, start, end, timezone, callback, urlLoadEvents);
            },


            eventMouseover: function (data, event, view) {
                calendarShowTooltip(data, event, view);
            },

            eventMouseout: function (data, event, view) {
                 $('#tooltip_event').remove();
            },
		});     
	});

</script>