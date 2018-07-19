<template>
    <div>
        <!-- Content Header (Page header) -->
			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark"><i class="fa fa-laptop"></i> Dashboard</h1>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<!-- Main content  -->
			<section class="content">
				<div class="container-fluid">
					<!-- Small boxes (Stat box) -->
					<div class="row">
						<div class="col-12 col-sm-6 col-md-3">
				            <div class="info-box">
				              <div class="info-box-content">
				                <span class="info-box-text">Tampilkan</span>
				                <span class="info-box-number">
				                  <select class="form-control"   @change="changePeriode" v-model="selectedPeriode">
														<option selected="selected" value="1">Sejak Awal</option>
														<option v-for="option in periode_options" v-bind:value="option.DATE" :key="option.DATE">
	                                    {{ option.DATE }}
	                            </option>
				                  </select>
				                </span>
				              </div>
				              <!-- /.info-box-content -->
				            </div>
				            <!-- /.info-box -->
				          </div>
									
				          <div class="col-12 col-sm-6 col-md-3">
				            <div class="info-box mb-3">
				              <span class="info-box-icon bg-success elevation-1"><i class="fa fa-file"></i></span>

				              <div class="info-box-content">
				                <span class="info-box-text">Cetak Laporan</span>
				                <span class="info-box-number">
													<a :href="'/report/lapor-layanan-aspirasi-dan-pengaduan-online/print-pdf'" class="btn btn-primary">
														Cetak <i class="fa fa-print"></i>
													</a>
												</span>
				              </div>
				              <!-- /.info-box-content -->
				            </div>
				            <!-- /.info-box -->
				          </div>
				          <!-- /.col -->
				          <div class="col-12 col-sm-6 col-md-3">
				            <div class="info-box mb-3">
				              <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-sign-in"></i></span>

				              <div class="info-box-content">
				                <span class="info-box-text">Terakhir Login</span>
				                <span class="info-box-number">{{last_login}}</span>
				              </div>
				              <!-- /.info-box-content -->
				            </div>
				            <!-- /.info-box -->
				          </div>
				          <div class="clearfix hidden-md-up"></div>

				          <!-- /.col -->
				          <div class="col-12 col-sm-6 col-md-3">
				            <div class="info-box mb-3">
				              <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-users"></i></span>

				              <div class="info-box-content">
				                <span class="info-box-text">Jumlah Kategori</span>
				                <span class="info-box-number">{{n_category}}</span>
				              </div>
				              <!-- /.info-box-content -->
				            </div>
				            <!-- /.info-box -->
				          </div>
						
					</div>
					<div class="row">
						<div class="col-6">
							<div class="card">
								<div class="card-body">
									<bar-chart 
										title="Jumlah data per Kategori"
										:url="distCategoryLine"></bar-chart>
								</div>								
							</div>							
						</div>

						<div class="col-6">
							<div class="card">
								<div class="card-body">
									<line-chart 
										title="Jumlah data Masuk"
										url="/api/dashboard/chart?data=freq-data"></line-chart>
								</div>								
							</div>							
						</div>

					</div>
					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-body">
									<pie-chart 
										title="Kategori"
										:url="distUrl"></pie-chart>
								</div>								
							</div>	
						</div>
					</div>
					<!-- /.row -->

					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<h5>Dashboard per Category</h5>
								</div>
								<div class="card-body">
									<router-link style="margin-right: 5px;margin-top: 5px;" class="btn btn-secondary"  v-for="(category, index) in categories"  v-bind:key="index" :to="{ name: 'dashboard-category', params: {slug:category.slug}}"  >   
										{{category.category}}
									</router-link>
								</div>								
							</div>							
						</div>
					</div>
				</div><!-- /.container-fluid -->
			</section>
			<!-- /.content -->
    </div>
</template>
<script>
	import moment from "moment";
	export default {

    	data() {
		    return {
					loading:true,		
					last_login:"loading...",
					n_category:"loading...",
					selectedPeriode:"1",
					periode_options:[],
					distCategoryLine:"/api/dashboard/chart?data=dist-category-line",
					distUrl:"/api/dashboard/chart/?data=dist-category",
		      categories:[]	      
		    };
		},

		created(){			
			this.fetch();
			this.fetchCategory();
		},

		methods:{
			fetchCategory(){
				axios.get('/api/categories')
			        .then(({data}) => {
				        this.categories = data;
				        this.loading = false;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
					   this.loading = false;
		        }); 
			},

			fetch(){
				axios.get('/api/dashboard')
			        .then(({data}) => {
								this.last_login = moment(data.last_login, "YYYY-MM-DD h:i:s").fromNow();
								this.n_category = data.n_category;
								
								this.periode_options = data.periode_options;
				      })
			        .catch((error) => {	 
		    }); 
			},

			changePeriode(){
				let periode = ""

				if(this.selectedPeriode!=1){					
					let periode_string = this.selectedPeriode.split("/");
					periode = "&month="+periode_string[0]+"&year="+periode_string[1];
				}
				this.distCategoryLine="/api/dashboard/chart?data=dist-category-line"+periode;
				this.distUrl="/api/dashboard/chart/?data=dist-category"+periode;
			},
		},

	
	}
</script>