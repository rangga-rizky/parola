<template>
	<div class="wrapper">
		<navbar></navbar>		
		<sidebar></sidebar>

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<div class="container">
				<transition name="fade">
				<router-view></router-view>
				</transition>
			</div>
		</div>
		<!-- /.content-wrapper -->

		<!-- Control Sidebar -->
		<aside class="control-sidebar control-sidebar-dark">
			<!-- Control sidebar content goes here -->
		</aside>
		<!-- /.control-sidebar -->
	</div>
	<!-- ./wrapper -->


</template>

<script>

export default {

        
 }
</script>
<style>
	.fade-enter-active, .fade-leave-active {
  transition-property: opacity;
  transition-duration: .25s;
}

.fade-enter-active {
  transition-delay: .25s;
}

.fade-enter, .fade-leave-active {
  opacity: 0
}
</style>