<template>
      <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <span class="brand-text font-weight-light">LAPOR</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="admin-lte/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Rangga Rizky</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              <li class="nav-item">
                <router-link :to="{ name: 'dashboard'}"   class="nav-link">                  
                    <i class="nav-icon fa fa-bar-chart-o"  :class="{ 'text-info': isActive('dashboard') }"></i>
                    <p>Dashboard</p>
                </router-link>                
              </li>
              <li class="nav-item">
                <router-link  :to="{ name: 'complaints'}"  class="nav-link" >
                  <i class="nav-icon  fa fa-file-o"   :class="{ 'text-info': isActive('complaints') }"></i>
                  <p>Data Pengaduan</p>
                </router-link>

              </li>

              <li class="nav-item">
                <router-link  :to="{ name: 'categories'}"  class="nav-link">
                  <i class="nav-icon fa fa-object-group"  :class="{ 'text-info': isActive('categories') }"></i>
                  <p>Daftar Kategori</p>
                </router-link>
              </li>

              <li class="nav-item">
                <a  class="nav-link">
                  <i class="nav-icon fa fa-table"  :class="{ 'text-info': isActive('product') }"></i>
                  <p>Daftar Kata Kunci
                  <i class="right fa fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                <!--
                  <li class="nav-item">
                    <router-link  :to="{ name: 'terms'}" class="nav-link">
                      <i class="fa fa-circle-o nav-icon" :class="{ 'text-info': isActive('terms') }"></i>
                      <p>Manual</p>
                    </router-link>
                  </li>
                  -->
                  <li class="nav-item">
                     <router-link  :to="{ name: 'training-terms'}" class="nav-link">
                      <i class="fa fa-circle-o nav-icon" :class="{ 'text-info': isActive('training-terms') }"></i>
                      <p>Aggregasi</p>
                     </router-link>
                  </li>
                </ul>
              </li>

               <li class="nav-item">
                <a   class="nav-link">
                  <i class="nav-icon fa fa-archive"></i>
                  <p>Data Latih
                   <i class="right fa fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <router-link  :to="{ name: 'training-score' }"  class="nav-link">
                      <i class="fa fa-circle-o nav-icon" :class="{ 'text-info': isActive('training-score') }"></i>
                      <p>Classification Report</p>
                    </router-link>
                  </li><!--
                  <li class="nav-item">
                    <router-link  :to="{ name: 'training-manual' }"  class="nav-link">
                      <i class="fa fa-circle-o nav-icon" :class="{ 'text-info': isActive('training-manual') }"></i>
                      <p>Manual Keyword</p>
                    </router-link>
                  </li>-->
                  <li class="nav-item">
                     <router-link  :to="{ name: 'training-auto' }"  class="nav-link">
                      <i class="fa fa-circle-o nav-icon" :class="{ 'text-info': isActive('training-auto') }"></i>
                      <p>Automatic Keyword</p>
                    </router-link>
                  </li>
                  <li class="nav-item">
                     <router-link  :to="{ name: 'training-bayes'}"  class="nav-link">
                      <i class="fa fa-circle-o nav-icon" :class="{ 'text-info': isActive('training-bayes') }"></i>
                      <p>Naive-Bayes</p>
                    </router-link>
                  </li>
                </ul>
              </li>

               <li class="nav-item">
                <router-link  :to="{ name: 'setting'}"  class="nav-link" >
                  <i class="nav-icon  fa fa-cogs"   :class="{ 'text-info': isActive('setting') }"></i>
                  <p>Pengaturan</p>
                </router-link>

              </li>              
              

        </ul>
      </nav>
          <!-- /.sidebar-menu -->
      </div>
        <!-- /.sidebar -->
    </aside>
</template>

<script>
    export default {
        methods: {
          isActive(menuItem) {
            return this.$route.name === menuItem;
          },
        } 
    }
</script>
