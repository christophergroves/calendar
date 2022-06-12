function initNameSearchBasic(url){

	var search = $("#nameSearchSelect");
	clearNameSearchSelect(search);
	search.change(function(){
		var srvUsrId  = search.find(":selected").val();
		getSrvUsrAjax(srvUsrId,url);
		clearNameSearchSelect(search);
		
    }); 
	
	$('#nextRecord').click(function(){
		var srvUsrId = $('#next-rec').val();
		if(!srvUsrId){return false;}
		getSrvUsrAjax(srvUsrId,url);
	});
	$('#prevRecord').click(function(){
		var srvUsrId = $('#prev-rec').val();
		if(!srvUsrId){return false;}
		getSrvUsrAjax(srvUsrId,url);
	});
}


function initNameSearchAllNames(url){

    var button = $('#btn_go');

    // handle user pressing enter
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            var data = $('#name_search_form').serialize();
            ajaxReplaceContent(url,data);
            return false;
        }
    });
    
    button.click(function(event){
        event.preventDefault();
        var data = $('#name_search_form').serialize();
        ajaxReplaceContent(url,data);
    });
}



function initNameSearchCalendarSelect2(width){
    
    var names = [{"id":"0","name":"loading"}];

	function format(item) { return item.name; };
    $("#nameSearchSel").select2({
            placeholder: "Name Search",
            data:{ results: names, text: 'name' },
			formatSelection: format,
			formatResult: format,
                }
                        
            ).on("change", function() { 
                srvUsrId  = $(this).select2('data').id;
                $('#calendar').fullCalendar( 'refetchEvents' );
//		var serviceUsrID = $(this).select2('data').id;
//		var serviceUsrName = $(this).select2('data').name;
//		nameSearchTimetable("#gridTimetable",serviceUsrID,serviceUsrName);
	});
	
    
    
    
//	var search = $("#nameSearchSelect");
//	clearNameSearchSelect(search);
//	search.change(function(){
//		srvUsrId  = $(this).select2('data').id;
//                $('#calendar').fullCalendar( 'refetchEvents' );
//		clearNameSearchSelect(search);
//		
//    }); 
	
	$('#nextRecord').click(function(){
		var srvUsrId = $('#next-rec').val();
		if(!srvUsrId){return false;}
		 $('#calendar').fullCalendar( 'refetchEvents' );
	});
	$('#prevRecord').click(function(){
		var srvUsrId = $('#prev-rec').val();
		if(!srvUsrId){return false;}
		getSrvUsrAjax(srvUsrId,url);
	});
}




function initNameSearchCalendarClassesSelect2(width){
    
    var names = [{"id":"0","name":"loading"}];

    function format(item) { return item.name; };
    $("#nameSearchSel").select2({
            placeholder: "Name Search",
            data:{ results: names, text: 'name' },
            formatSelection: format,
            formatResult: format,
                }
                        
            ).on("change", function() { 
                tutorId  = $(this).select2('data').id;
                $('#calendar').fullCalendar( 'refetchEvents' );
    });
    
    $('#nextRecord').click(function(){
        var srvUsrId = $('#next-rec').val();
        if(!srvUsrId){return false;}
         $('#calendar').fullCalendar( 'refetchEvents' );
    });
    $('#prevRecord').click(function(){
        var srvUsrId = $('#prev-rec').val();
        if(!srvUsrId){return false;}
        getSrvUsrAjax(srvUsrId,url);
    });
}

function initNameSearchCalendarButtons(){
    $('#prevRecord').click(function(){
        if(prevRec){
            srvUsrId = prevRec;
            $('#calendar').fullCalendar( 'refetchEvents' );
        }
    });
    $('#nextRecord').click(function(){
        if(nextRec){
            srvUsrId = nextRec;
            $('#calendar').fullCalendar( 'refetchEvents' );
        }
    });
}


function refreshCalendarNameSearchSelect2(returnedData){
    
    if (returnedData.names) {
        
        var nameSearch = returnedData.names;
        var optionString = '[';
        var firstLetterStored = false;
        var newFirstLetter = false;
        
        for (i in nameSearch) {
           
            var firstLetter = nameSearch[i]['name'].charAt(0);
            
            if(firstLetterStored !== firstLetter){
                firstLetterStored = firstLetter;
                newFirstLetter = true;
                
                if(i > 0){optionString += ']},';}
                optionString += '{"text":' + '"' + firstLetter + '"' + ',' + '"children":' + '[';
            }
            if(i > 0 && !newFirstLetter){
                optionString += ',';
            }
            optionString +=  '{"id":' + '"' + nameSearch[i]['id'] + '"' + ',' + '"text":' + '"' + nameSearch[i]['name'] + '"}';
            newFirstLetter = false;
        };
        optionString += ']}]';


        // if ajax returned data contains no names
        if(optionString === '[]}]'){
        	var names = null;
        }else{
        	var names = jQuery.parseJSON(optionString);
        }
        
	function format(item) { return item.text; };
        $("#nameSearchSel").select2({
            placeholder: "Name Search",
            data:{ results: names, text: 'text' },
                formatSelection: format,
                formatResult: format,
        });
    }
    
    nextRec = returnedData.next_rec;
    prevRec = returnedData.prev_rec;
}

function refreshCalendarClassesDescription(returnedData,start,end){


     $('#service_user_name').html(returnedData.tutor.name);
     // $('#project_officer').html(returnedData.activity_class.project_officer);
     $('#project_officer').html('');
}



function refreshServiceUserDescription(returnedData,start,end){

    var startLeaveDateReachedMessage = null;
    var leaveDateReached = false;
    var startDateReached = false;
    var calStart = date2mysql(start);
    var calEnd = date2mysql(end);
    var srvUsrMinStartDate = returnedData.service_user.start_date;
    // var srvUsrMinStartDateLong = new Date(returnedData.service_user.min_start_date);
    // alert(srvUsrMinStartDateLong.moment().format('Do MMM YYYY'));
    var srvUsrLeaveDate = returnedData.service_user.leave_date;

    // clear the start date / leave date reached warning (e.g. cal month before start date)
    $('#start_leave_date_reached').empty();

    if(compareDatesMysql(srvUsrMinStartDate, calStart)){
        startDateReached = true;
    }
    if(srvUsrLeaveDate !== null){
        if(compareDatesMysql(calEnd,returnedData.service_user.leave_date)){
            leaveDateReached = true;
        }
    }

    if(startDateReached){
         $('#start_leave_date_reached').html('<h4 style="color:red;margin-left:5px;"><strong>&#42;Start Date: ' + mysql2dmy(srvUsrMinStartDate) + '</strong></h4>');
    }else if(leaveDateReached){
        $('#start_leave_date_reached').html('<h4 style="color:red;margin-left:5px;"><strong>&#42;Leave Date: ' + mysql2dmy(srvUsrLeaveDate) + '</strong></h4>');
    }


    $('#service_user_name').html(returnedData.service_user.name);
   
    var proj_officer = returnedData.service_user.project_officer ? '('+ returnedData.service_user.project_officer + ')' : ' ';
    $('#project_officer').html('&nbsp' + proj_officer);

}


function getSrvUsrAjax(srvUsrId,url){
	
	$.ajax({
		url: url + '/' + srvUsrId,
		data: '',
		dataType: 'html',
		tryCount:0,//current retry count
		retryLimit:3,//number of retries on fail
		timeout: 3000,//time before retry on fail
		success: function(returnedData) {
			$('#content-replaceable').html(returnedData);//put the returned html into the div
		},
		error: function(xhr, textStatus, errorThrown) {
			if (textStatus == 'timeout') {//if error is 'timeout'
				this.tryCount++;
				if (this.tryCount < this.retryLimit) {
					$.ajax(this);//try again
					return;
				}
			}//after 3 retries to get content form server alert error message
			//alert('error: please refresh the page');
            if(textStatus === 'error'){redirectToLogin(url);}
		}
	});
}





function clearNameSearchSelect(select,delay) {
        delay = delay ? delay : 300;
	setTimeout(function(form) {
		$(select).select2("val", "");
	}, delay)
}

function isInt(n) {
   return typeof n === 'number' && n % 1 == 0;
}

function getURLParameter(name) {
    return decodeURI(
		  (RegExp(name + '=' + '(.+/)(&|$)').exec(location.search)||[,null])[1]
    );
}