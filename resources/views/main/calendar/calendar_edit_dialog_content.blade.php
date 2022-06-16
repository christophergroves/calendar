<script type="text/javascript">
    
    // to cure datepicker disable on first load issue, var used in calendarEditDialogRepeatSectionSlider function.
//    var sessionDatePickerInitialised = false;


    var sessionID = "{!! $session->id !!}";
    if (sessionID === ''){sessionID = 0;}
    var url = "{!! URL::to('') !!}";
    var fp = "{!! Session::get('fp') !!}";




     var calEvent = 
    {
        id: sessionID,
        start: "{{ $session_date }}",
        recurrance_type: "{{ $session->recurrance_type }}",
        parent_id: "{{ $session->parent_id }}",
        user_id: {{ $user->id }},
    }



    $('#activity_add').click(function(event){
        event.preventDefault();
        calendarEditSessionDialog.close();
        calendarEditSessionDialog = false;
        openCalendarActivityEdit(fp,calEvent, url);
    });





   
    initDatePicker('#start_date');
    initDatePicker('#session_date');
    initDatePicker('#finish_date');

    initSelect2('#activity_id', 500);

    var origSessionDate = $('#session_date').val();
    var dialogTitle = "{!! $edit_dialog_title !!}";
    var recurrance_type = $('#recurrance_type').val();

    calendarEditDialogRepeatSectionSlider($('#recurrance_type').val(),dialogTitle)

    $('#recurrance_type').change(function() {
        calendarEditDialogRepeatSectionSlider($(this).val(),dialogTitle);
        if(recurrance_type === '0'){
            $('#finish_date').val('');
        }
    });
    
    


$(document).ready(function() {


    $('#recurrance_number').focus(function(){
        $("#ends_on_occurances").prop('checked',true);
    });

    $('#finish_date').focus(function(){
        $("#ends_on_date").prop('checked',true);
    });


    $('#finish_date-btn').click(function(){
        $("#ends_on_date").prop('checked',true);
    });





    $('#calendar_edit_form').bootstrapValidator({
        message: 'This value is not valid',
//        feedbackIcons: {
//            valid: 'glyphicon glyphicon-ok',
//            invalid: 'glyphicon glyphicon-remove',
//            validating: 'glyphicon glyphicon-refresh'
//        },
        fields: {
            activity_id: {
                validators: {
                    notEmpty: {
                        message: 'An activity must be selected'
                    },
                }
            },
            session_date: {
                validators: {
                    notEmpty: {
                        message: 'Event date is required'
                    },
                    date: {
                        format: 'DD/MM/YYYY',
                        message: 'Must be in DD/MM/YYYY format'
                    }
                }
            },
            start_time: {
                validators: {
                    regexp: {
                        regexp: /([0-2]?\d)[:]([0-5]\d)/,
                        message: 'hh:mm 24hr format only'
                    }
                }
            },
            finish_time: {
                validators: {
                    regexp: {
                        regexp: /([0-2]?\d)[:]([0-5]\d)/,
                        message: 'hh:mm 24hr format only'
                    }
                }
            },
            start_date: {
                validators: {
                    notEmpty: {
                        message: 'Event date is required'
                    },
                    date: {
                        format: 'DD/MM/YYYY',
                        message: 'Must be in DD/MM/YYYY format'
                    }
                }
            },
            recurrance_number: {
                validators: {
                    digits: {
                        message: 'Occurances must be an number'
                    },
                   between: {
                        min: 1,
                        max: 36,
                        message: 'Must be between 1 and 36'
                    }

                }
            },
            finish_date: {
                validators: {
                    date: {
                        format: 'DD/MM/YYYY',
                        message: 'Must be in DD/MM/YYYY format'
                    }
                }
            },
        }
    });
});

</script>


<style>

</style>






{!! Form::open(array('id'=>'calendar_edit_form','class'=>'form-horizontal','role'=>'form')) !!}


    <div class="form-group">
        {!! Form::label('activity_id_label','Activity',array('class'=>'col-xs-4 control-label')); !!}
        <div class="col-xs-20">
            {!! Form::select('activity_id', $activities, $session->activity_id, array('id' => 'activity_id')); !!} <span class="form-asterisk">*</span>

            {!! HTML::link('#', 'Add', array('id'=> 'activity_add')); !!}
      
        </div>
    </div>


    <div class="spacer10px"></div>




    {!! FormElement::textDatepicker('session_date', 'Session Date:', SiteService::mysql2dmy($session_date), $errors, true, array('form-group-class'=>'start_date_form_group','placeholder' => 'Enter event start date (dd/mm/yyyy)','style'=>'z-index: 1050;')+$disabled_elements,array('label'=>'xs-4','control'=>'xs-20 date_picker')); !!}
    

    <div class="form-group">
        <div class="spacer5px"></div>
        <span class="col-xs-4 control-label inline-elements-label">Start Time:</span>
        <div class="col-xs-20">
            <div class="form-group row">
                <div class="col-xs-4">
                    {!! Form::text('start_time',$session->start_time,array('id'=>'start_time','class'=>'display-inline form-control','placeholder'=>'hh:mm')); !!}
                </div>
                {!! Form::label('finish_time','Finish Time:',array('class'=>'col-xs-5 display-inline control-label')); !!}
                <div class="col-xs-6">
                    {!! Form::text('finish_time',$session->finish_time,array('id'=>'finish_time','class'=>'display-inline form-control','placeholder'=>'hh:mm')); !!}
                </div>
            </div>
        </div>
    </div>

    {!! FormElement::select('hours', 'Hours:', $session->hours, $hours, $errors, false,array(),array(),array('label'=>'xs-4','control'=>'xs-19')); !!}

    <div class="spacer5px"></div>
    
    
@if($edit_action !== 'edit-instance')

    <?php FormElement::select('recurrance_type', 'Repeats:', $session->recurrance_type, $recurrance_types, $errors, false, [], [], ['label' => 'xs-4', 'control' => 'xs-19']); ?>
    

    <div class="repeats-section" style="display:none;">
    
        <div class="spacer5px"></div>
        <hr class="hr-md">
        
         <?php FormElement::selectTextAfter('recurrance_interval', 'Every:', $session->recurrance_interval, $recurrance_intervals, $errors, false, [], ['text' => 'Days', 'class' => 'recurrance-interval-text'], ['label' => 'xs-4', 'control' => 'xs-19']); ?>

       

        <!--<h5 class="text-primary text-bold">Repeats...</h5>-->

           <div class="recurrance_weekly" style="display:none;">
            <div class="spacer5px"></div>
            <div class="row">
                <div class="col-xs-4 control-label"><label>On Day:</label></div>
                <div class="col-xs-20 session_day_checkboxes">
                    {!! Form::checkbox('recurrance_day[]','1',$session_days[1],array('class'=>'recurrance_day')) !!}<label class="checkbox-label">Mon</label>
                    {!! Form::checkbox('recurrance_day[]','2',$session_days[2],array('class'=>'recurrance_day')) !!}<label class="checkbox-label">Tue</label>
                    {!! Form::checkbox('recurrance_day[]','3',$session_days[3],array('class'=>'recurrance_day')) !!}<label class="checkbox-label">Wed</label>
                    {!! Form::checkbox('recurrance_day[]','4',$session_days[4],array('class'=>'recurrance_day')) !!}<label class="checkbox-label">Thu</label>
                    {!! Form::checkbox('recurrance_day[]','5',$session_days[5],array('class'=>'recurrance_day')) !!}<label class="checkbox-label">Fri</label>
                    {!! Form::checkbox('recurrance_day[]','6',$session_days[6],array('class'=>'recurrance_day')) !!}<label class="checkbox-label">Sat</label>
                    {!! Form::checkbox('recurrance_day[]','7',$session_days[7],array('class'=>'recurrance_day')) !!}<label class="checkbox-label">Sun</label>

                </div>
            </div>
            <div class="spacer20px"></div>
        </div>
        
       
        

       
        <div class="recurrance_monthly" style="display:none;">
            <div class="form-group">
                <span class="col-xs-4 control-label inline-elements-label">On the:</span>
                <div class="col-xs-19">
                    <div class="form-group row">
                        <div class="col-xs-4 recurrance_monthly_interval_column">
                            {!! Form::select('recurrance_monthly_interval', $recurrance_monthly_intervals,  $session->recurrance_monthly_interval, array('id'=> 'recurrance_monthly_interval','class' => 'form-control'));  !!}
                        </div>
                        <div class="col-xs-15">
                            {!! Form::select('recurrance_day_single', $recurrance_days,  $session->session_day, array('id'=> 'recurrance_day_single', 'class' => 'display-inline form-control'));  !!}
                            <span class="display-inline text-bold">of the month</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="spacer10px"></div>
        </div>


      


    {!! FormElement::textDatepicker('start_date', 'Starts On:', SiteService::mysql2dmy($session->start_date), $errors, true, array('form-group-class'=>'start_date_form_group','placeholder' => 'Enter event start date (dd/mm/yyyy)','style'=>'z-index: 1050;')+$disabled_elements,array('label'=>'xs-4','control'=>'xs-19 date_picker')); !!}

    
    
     <div class="ends_on">
            <div class="form-group row">
                <div class="col-xs-4 control-label"><label>Ends:</label></div>
                <div class="col-xs-20 ends_on_radio_btns">
                    
                    
                        
                    <div class="form-group">
                    {!! Form::radio('ends_on','ends_never',$ends_on[0],array('id'=>'ends_never')) !!}<label for="ends_never" class="radio-label">&nbsp;Never</label>
                    </div>
               
                    
                    <div class="form-group">
                    
                    {!! Form::radio('ends_on','ends_on_occurances',$ends_on[1],array('id'=>'ends_on_occurances', 'class' => 'display-inline')) !!}
                        <label for="ends_on_occurances" class="radio-label display-inline">&nbsp;After</label>
                    {!! Form::text('recurrance_number',$session->recurrance_number,array('id'=>'recurrance_number', 'class'=>'display-inline form-control','placeholder'=>'Number'))!!}
                        <p class="display-inline">&nbsp;occurrences</p>
                    </div>
                    
                    <div class="form-group date_picker2">
                              
                         <!--<div class="spacer5px"></div>-->
                    {!! Form::radio('ends_on','ends_on_date',$ends_on[2],array('id'=>'ends_on_date','class' => 'display-inline pull-left')) !!}<label for="ends_on_date" class="radio-label display-inline pull-left">&nbsp;On</label>
                    
                    
                    <div class="input-group pull-left finish_date_form_group">
                        {!! Form::text('finish_date',SiteService::mysql2dmy($session->finish_date),array('id'=>'finish_date', 'class'=>'display-inline form-control','placeholder'=>'Finish date')) !!}
                        <span class="input-group-addon" id="finish_date-btn" style="cursor:pointer;"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    
    @endif

{!! Form::close() !!}



