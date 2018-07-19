<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Parola</title>

	 <!-- Font Awesome -->
	 <link href="https://fonts.googleapis.com/css?family=Poppins:300,500,600" rel="stylesheet">
    <link rel="stylesheet" href="{{url('admin-lte/plugins/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('admin-lte/plugins/datatables/dataTables.bootstrap4.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{url('admin-lte/css/adminlte.min.css')}}">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	
	<!-- Google Font: Source Sans Pro -->
</head>
<body class="hold-transition sidebar-mini">
	<div id="app" style="width:100%">
		<index></index>
	</div>
</body>
<script src="{{  URL::asset('js/app.js') }}"></script>
<!-- jQuery -->
<script src="{{  URL::asset('admin-lte/plugins/jquery/jquery.min.js') }}" ></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Sparkline -->
<script src="{{  URL::asset('admin-lte/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
<!-- Slimscroll -->
<script src="{{  URL::asset('admin-lte/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{  URL::asset('admin-lte/plugins/fastclick/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{  URL::asset('admin-lte/js/adminlte.js') }}"></script>
<script src="{{  URL::asset('admin-lte/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{  URL::asset('js/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{  URL::asset('js/scrollreveal/scrollreveal.min.js') }}"></script>
<script src="{{  URL::asset('js/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script src="{{  URL::asset('js/creative.min.js') }}"></script>

</html>