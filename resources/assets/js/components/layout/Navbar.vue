<template>
 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="#" class="nav-link">Home</a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
   <li class="nav-item">
      <button @click="logout" class="btn btn-danger">
        Sing out <i class="fa fa-sign-out"></i>
      </button>
   </li>    
  </ul>

</nav>
      <!-- /.navbar -->

</template>

<script>

export default {
   methods: {
    logout() {
      this.$store.dispatch("auth/logoutUser").then(() => {        
        this.$router.push("/login")
      });
    },
       
  },
}
</script>