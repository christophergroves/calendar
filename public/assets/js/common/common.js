
function initDatePicker(id) {

    $(id + '-btn').on('click',function()
{
	// $(id + '-btn').click(function() {

		//make the button act as a toggle
		var datepickerDiv = $('#ui-datepicker-div');
                
		if(datepickerDiv.is(':visible')){
			$(id).datepicker('hide');
		}else{
			$(id).datepicker({
				showOn: "button",
				onClose: function(){$(this).attr('disabled',false);},
				beforeShow: function(){$(this).attr('disabled',true);},
				buttonImageOnly: true,
				dateFormat: "dd/mm/yy",
				firstDay: 1,
			}).datepicker('show');
		}//hack to prevent datepicker image appearing because not useing it with bootstrap icon
		$('.ui-datepicker-trigger').remove();
	});
}



function slideDownDateOnObjectiveStatusChange(element)
{
    var stepStatus = $(element).val();
    var slideDownAchievedDateDivID ='#achieved_date_'+$(element).attr('id')+'-div';

    if(stepStatus === "2")
    {   
        $(slideDownAchievedDateDivID).slideDown();
    }else{
        $(slideDownAchievedDateDivID).slideUp();
    }
}



function scrollToTableID(scroll_id){

    // get the parent row and toggle class .highlight
    var tr = $( '#' + scroll_id ).closest( 'tr' );
    tr.toggleClass( 'highlight' ); 
    // find any child rows with the class .row_content and toggle highlight on them too (nested objective steps table)
    tr.find('tr.row_content').toggleClass( 'highlight' );
    // scroll down the page so that the id of the row that has been edited is in the middle of the page
    $('html,body').animate({
        scrollTop: $('#' + scroll_id).offset().top -  $(window).height() / 2
    }, 1000);
    // remove class .highlight after period of seconds
    setTimeout(function(){$(".highlight").removeClass('highlight');},8000);
}


function scrollToIDNav(scroll_id,height_offset=3,speed=300){

    // scroll down the page so that the id of the row that has been edited is in the middle of the page
    $('html,body').animate({
        scrollTop: $('#' + scroll_id).offset().top -  $(window).height() / height_offset
    }, speed);
}



function slideDownSubTable(sub_table_id){
    $('#' + sub_table_id).slideDown(0);
    $('#view_' + sub_table_id).removeClass('glyphicon-chevron-right');
    $('#view_' + sub_table_id).addClass('glyphicon-chevron-down');
}



function objectiveReviewsSlideUpDownAll(linkButton){

        if( $(linkButton).hasClass('objective_reviews_shown') ){
            $('.objective_reviews_show_hide').removeClass('glyphicon-chevron-down');
            $('.objective_reviews_show_hide').addClass('glyphicon-chevron-right');
            $(linkButton).removeClass('objective_reviews_shown');
            $(linkButton).addClass('objective_reviews_hidden');
            $(linkButton).text('Show All Reviews / Updates');

            $('.sub_table_objectives_div').slideUp(300);
        }else{
            $('.objective_reviews_show_hide').removeClass('glyphicon-chevron-right');
            $('.objective_reviews_show_hide').addClass('glyphicon-chevron-down');
            $(linkButton).removeClass('objective_reviews_hidden');
            $(linkButton).addClass('objective_reviews_shown');
            $(linkButton).text('Hide All Reviews / Updates');
            $('.sub_table_objectives_div').slideDown(300);
        }
    }


    function riskAssessmentsSlideUpDownAll(linkButton){

        if( $(linkButton).hasClass('risk_assessments_shown') ){
            $('.risk_assessments_show_hide').removeClass('glyphicon-chevron-down');
            $('.risk_assessments_show_hide').addClass('glyphicon-chevron-right');
            $(linkButton).removeClass('risk_assessments_shown');
            $(linkButton).addClass('risk_assessments_hidden')
            $(linkButton).text('Show Risks');
            $('.sub_table_risks_div').slideUp(300);
        }else{
            $('.risk_assessments_show_hide').removeClass('glyphicon-chevron-right');
            $('.risk_assessments_show_hide').addClass('glyphicon-chevron-down');
            $(linkButton).removeClass('risk_assessments_hidden');
            $(linkButton).addClass('risk_assessments_shown');
            $(linkButton).text('Hide Risks');
            $('.sub_table_risks_div').slideDown(300);
        }
    }


    function activitySessionsSlideUpDownAll(linkButton){

        if( $(linkButton).hasClass('activity_sessions_shown') ){
            $('.activity_sessions_show_hide').removeClass('glyphicon-chevron-down');
            $('.activity_sessions_show_hide').addClass('glyphicon-chevron-right');
            $(linkButton).removeClass('activity_sessions_shown');
            $(linkButton).addClass('activity_sessions_hidden')
            $(linkButton).text('Show All Sessions');
            $('.sub_table_activities_div').slideUp(300);
        }else{
            $('.activity_sessions_show_hide').removeClass('glyphicon-chevron-right');
            $('.activity_sessions_show_hide').addClass('glyphicon-chevron-down');
            $(linkButton).removeClass('activity_sessions_hidden');
            $(linkButton).addClass('activity_sessions_shown');
            $(linkButton).text('Hide All Sessions');
            $('.sub_table_activities_div').slideDown(300);
        }
    }


    function goalStepStatusAchivedDateSlideUpDown(){
        
    }


function monitorForInputChange(inputID){
    var target = $(inputID),
    val = target.val();

    function monitor()
    {
        var current_val = $(this).val();
        if (current_val != val) {
            console.log('changed from', val, 'to', current_val);
            val = current_val;
        }
    }
    target.keypress(monitor);
}

function disableEnableDatePicker(id,state){
    
     $(id).datepicker({
                showOn: "button",
                onClose: function(){$(this).attr('disabled',false);},
                beforeShow: function(){$(this).attr('disabled',true);},
                buttonImageOnly: true,
                dateFormat: "dd/mm/yy",
                firstDay: 1}).datepicker(state);
            $('.ui-datepicker-trigger').remove();
    
}

function initSelect2(selectName, width) {
	$(selectName).select2({
		//dropdownAutoWidth:true,
		width: width
	});
}



function initAjaxLoadingSpinnerReport(){
    
    //$body = $("body");
    $spinner = $('.ajax-spinner')
    $(document).on({
        ajaxStart: function() {

            if($('#content-replaceable').html() !== ''){
                return false;
            }
            // $body.addClass("loading");
            // $spinner.addClass('loading');
            $spinner.addClass('sk-fading-circle');
        },
        ajaxStop: function() {
            // $body.removeClass("loading");
            // $spinner.removeClass('loading');
            $spinner.removeClass('sk-fading-circle');
        }
    });
}


function initAjaxLoadingSpinner(){
    
    // $('#content-replaceable').html("");
    //$body = $("body");
    $spinner = $('.ajax-spinner')
    $(document).on({
        ajaxStart: function() {
            
            // $body.addClass("loading");
            // $spinner.addClass('loading');
            $spinner.addClass('sk-fading-circle');
        },
        ajaxStop: function() {
            // $body.removeClass("loading");
            // $spinner.removeClass('loading');
            $spinner.removeClass('sk-fading-circle');
        }
    });
}








function initFadeAlert(){
    setTimeout(function(){
        $(".alert-success").fadeOut("slow", function(){
            $(".alert-success").empty();
        });
    }, 6000); 

    setTimeout(function(){
        $(".alert-warning").fadeOut("slow", function(){
            $(".alert-warning").empty();
        });
    }, 20000);  


}

function initNavbarSearchBox(id){
	$(id + '-btn').click(function() {
		$(id+ '-form').submit();
	});
}

function initButtonDisableOnClick(){
	
	$btnSave = $('.btn-save');
	$btnYes = $('.btn-yes')
	$btnSave.click(function(){disableButton($btnSave);});
	$btnYes.click(function(){disableButton($btnYes);});
}


function disableSubmitFormOnEnter(){
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

}

function disableButton(button){
	
  	$('.form-horizontal').submit(); 
  	$('.form-inline').submit(); 
    $('.form_custom').submit();
	button.attr('disabled','disabled');
	setTimeout(function() {
		button.removeAttr("disabled")
	}, 10000)
}


    function openAlertDialog(title,message,typeNo){

        var types = [BootstrapDialog.TYPE_DEFAULT, 
                 BootstrapDialog.TYPE_INFO, 
                 BootstrapDialog.TYPE_PRIMARY, 
                 BootstrapDialog.TYPE_SUCCESS, 
                 BootstrapDialog.TYPE_WARNING, 
                 BootstrapDialog.TYPE_DANGER];


        BootstrapDialog.show({
            type: types[typeNo],
            title: title,
            message: message,
            buttons: [{
                label: 'OK',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
    }










function ajaxReplaceContent(url,data){

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
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
            } // after 3 retries to get content form server alert error message
            alert('error: please refresh the page and try again');
        }
    });
}






function mysql2dmy(date) {
    var dateParts = date.split("-");
    var d = new Date(dateParts[0], dateParts[1] - 1, dateParts[2].substr(0,2));
    var curr_date = d.getDate();
    var curr_month = d.getMonth() + 1; //Months are zero based
    var curr_year = d.getFullYear();
    var dateDMY = (curr_date + "/" + curr_month + "/" + curr_year);
    return dateDMY;
}


function dmy2mysql(date) {
    var dateParts = date.split("/");
    var d = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
    var curr_date = d.getDate();
    var curr_month = d.getMonth() + 1; //Months are zero based
    var curr_year = d.getFullYear();
    var dateYMD = (curr_year + "-" + curr_month + "-" + curr_date);
    return dateYMD;
}


function date2dmy(date){
    var d = new Date(date);
    var curr_date = d.getDate();
    var curr_month = d.getMonth() + 1; //Months are zero based
    var curr_year = d.getFullYear();
    var dateDMY = (curr_date + "/" + curr_month + "/" + curr_year);
    return dateDMY;
}

function date2mysql(date){
    var d = new Date(date);
    var curr_date = d.getDate();
    var curr_month = d.getMonth() + 1; //Months are zero based
    var curr_year = d.getFullYear();
    var dateYMD = (curr_year + "-" + curr_month + "-" + curr_date);
    return dateYMD;
}

function compareDatesMysql(date1,date2){

    if(!date1 || !date2){
        return false;
    }

    var dateParts1 = date1.split("-");
    var dateParts2 = date2.split("-");

    var d1 = new Date( dateParts1[0],  dateParts1[1] - 1, dateParts1[2].substr(0,2)  );
    var d2 = new Date( dateParts2[0],  dateParts2[1] - 1, dateParts2[2].substr(0,2)  );

    // alert(d1);

    if( d1 > d2 ){
        return true;
    }else{
        return false;
    }
}




// Checks a string to see if it in a valid date format
// of (D)D/(M)M/(YY)YY and returns true/false
function checkDateUK(s) {
    // format D(D)/M(M)/(YY)YY
    var dateFormat = /^\d{1,4}[\.|\/|-]\d{1,2}[\.|\/|-]\d{4}$/;

    if (dateFormat.test(s)) {
        // remove any leading zeros from date values
        s = s.replace(/0*(\d*)/gi,"$1");
        var dateArray = s.split(/[\.|\/|-]/);
      
        // correct month value
        dateArray[1] = dateArray[1]-1;

        // correct year value
        if (dateArray[2].length<4) {
            // correct year value
            dateArray[2] = (parseInt(dateArray[2]) < 50) ? 2000 + parseInt(dateArray[2]) : 1900 + parseInt(dateArray[2]);
        }

        var testDate = new Date(dateArray[2], dateArray[1], dateArray[0]);
        if (testDate.getDate()!=dateArray[0] || testDate.getMonth()!=dateArray[1] || testDate.getFullYear()!=dateArray[2]) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}


function initOpenSimpleAjaxDialog(url,title,btnLinkId,formId,btnLblSave,successCallback,fp){

    $(btnLinkId).click(function(){

        var urlLoad = url + '/dialog_content/' +  fp;
        var urlSet = url + '/save/' + fp;

          BootstrapDialog.show({

            title: title,
            message: $('<div></div>').load(urlLoad,function(response,textStatus){
                    if(textStatus === 'error'){redirectToLogin(url);}
                }),
            draggable: false,
             buttons: [{
                label: btnLblSave,
                cssClass: 'btn-primary pull-left',
                action: function(dialog) {
                    var data = $(formId).serialize();
                    postSimpleAjaxDialog(urlSet, data, successCallback);
                    dialog.close();
                }
            },{
                label: 'Cancel',
                cssClass: 'btn-default pull-left',
                action: function(dialog) {
                    dialog.close();
                }
            }]
        });
    });
}


function postSimpleAjaxDialog(url,data,successCallback){

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        tryCount:0,//current retry count
        retryLimit:3,//number of retries on fail
        timeout: 3000,//time before retry on fail
        success: function(returnedData) {
            if(typeof successCallback === "function"){ successCallback() }
        },
        error: function(xhr, textStatus, errorThrown) {
            if (textStatus == 'timeout') {//if error is 'timeout'
                this.tryCount++;
                if (this.tryCount < this.retryLimit) {
                    $.ajax(this);//try again
                    return;
                }
            } // after 3 retries to get content form server alert error message
            alert('error: please refresh the page and try again');
        }
    });
}



function redirectToLogin(url){
    // window.location.replace(url);
    // window.location.replace('https://ims-cedar.org');
}











	


        



