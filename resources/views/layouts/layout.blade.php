<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	@include('partials.common.links')
	@yield('view_specific_links')
	@yield('view_specific_css')
</head>

	<body>

		<div class="content-wrapper">
			
			<div class="container">

						<div class="well">

							@yield('title')
							@yield('section_1')

						</div>

			</div> <!--end container-->   

		</div><!--end content-wrapper--> 
		
		
		@yield('footer_scripts')

	</body>

</html>