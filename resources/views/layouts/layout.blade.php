<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	@include('partials.common.links')

	@yield('view_specific_links')
</head>

	<body>

		<div class="content-wrapper">
			
			<div class="container">
						
						<div class="well">

							<h1>CALEDAR VIEW</h1>

							@yield('section_1')

						</div>

			</div> <!--end container-->   

		</div><!--end content-wrapper--> 
		
		
		@yield('footer_scripts')

	</body>

</html>