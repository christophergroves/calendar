<!DOCTYPE html>
<html lang="en">
<head>

	<!--======================= Headers ==============================-->
	@include('partials.site.header')
	
	@include('partials.site.links_common')

	<title>@yield('title')</title>

	@yield('css')
	@yield('js1')
	@yield('js2')
	

</head>
<body>

	<div class="content-wrapper">
           
		@include('partials.nav.nav_navbar')



		<div class="view-heading">
			@include('partials.site.view_heading')
		</div>
		
		

		<div class="container">
                    
                    <div class="nav-wrapper">
			@yield('paginator1')
			@include('partials.nav.nav_names_calendar')
                    </div>


		            @if(Session::has('message'))
						<div class="alert alert-dismissable {!! Session::get('message_class')!!}">
							  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							  {!! Session::get('message') !!}
						</div>
					@endif

			
                    <div class="well">

                            @yield('token')
                            @yield('description')
                            @yield('section1')

                            <div class="clear-float"></div>

                            @yield('modal1')
                    </div>


                    <div style="height:300px;"></div>

			

                    <div class="push"></div>
		</div> <!--end container-->   

	</div><!--end content-wrapper-->   
	<div class="footer"></div><!--end footer-->   


	<!--======================= Javascript Common ==============================-->
	@include('partials.site.javascript_common')


	<!--============================ Calendar js and css =========================================-->
	<link rel="stylesheet" type="text/css" href="{!! asset('assets/javascripts/fullcalendar-2.4.0/fullcalendar.css') !!}"/>
	<script type="text/javascript" src="{!! asset('assets/javascripts/fullcalendar-2.4.0/lib/moment.min.js') !!}"></script>
	<script type="text/javascript" src="{!! asset('assets/javascripts/fullcalendar-2.4.0/fullcalendar.min.js') !!}"></script>
    <!--============================ jquery validation engine =========================================-->
   
   
    <!--============================ boostrap validator =========================================-->
    <link rel="stylesheet" href="{!! asset('assets/javascripts/bootstrapvalidator-dist-0.5.3/dist/css/bootstrapValidator.min.css') !!}"/>
    <script type="text/javascript" src="{!! asset('assets/javascripts/bootstrapvalidator-dist-0.5.3/dist/js/bootstrapValidator.min.js') !!}"></script>
	

	<!--============================ JQuery Tooltip =========================================-->
	<link rel="stylesheet" href="{!! asset('assets/javascripts/jquery.qtip/jquery.qtip.min.css') !!}"/>
	<script type="text/javascript" src="{!! asset('assets/javascripts/jquery.qtip/jquery.qtip.min.js') !!}"></script>

        
    <!--============================ General Site js and css =========================================-->
	<script type="text/javascript" src="{!! asset('assets/javascripts/common/name_search.js?v=1.2') !!}"></script>
	<script type="text/javascript" src="{!! asset('assets/javascripts/common/calendar.js?v=1.8') !!}"></script>


</body>



</html>