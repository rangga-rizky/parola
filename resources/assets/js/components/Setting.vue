<template>
    <div>
        <!-- Content Header (Page header) -->
			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark"><i class="fa fa-cogs"></i>  Pengaturan</h1>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<!-- Main content -->
			<section class="content">
				<div class="container-fluid">
					<!-- Small boxes (Stat box) -->
					<div class="row">
						<div class="col-6">
							<div class="card">
								<div class="card-header">
									<h6>Data Latih</h6>
								</div>
								<div class="card-body">
									<table class="table table-bordered">
									<tr>
										<td>
											Reset Data Latih
										<td>
											<button class="btn btn-danger" :disabled="disabled == true">
												<i class="fa fa-undo"></i> Reset
											</button>
										</td>
									</tr>
									<tr>
										<td>Tambah Data Latih</td>
										<td>
											<button class="btn btn-primary" :disabled="disabled == true">
												<i class="fa fa-cloud-upload"></i> Tambah
											</button>
										</td>
									</tr>	
									<tr>
										<td>Semua Data sudah di pra-proses</td>
										<td>
											<button @click="clean_data" class="btn btn-success" :disabled="disabled == true">
												<span v-if="isPending == 'clean_data'"><i class="fa fa-circle-o-notch fa-spin"> </i> Loading</span>
												<span v-if="isPending != 'clean_data'"><i class="fa fa-cog"></i> Pre-processing Data</span>
											</button>
										</td>
									</tr>								
									</table>
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="card">
								<div class="card-header">
									<h6>Naive-Bayes</h6>
								</div>
								<div class="card-body">
									<table class="table table-bordered">	
									<tr>
										<td>Binary Matriks ditemukan</td>
										<td>
											<button @click="generate_bin_matrice" class="btn btn-primary" :disabled="disabled == true">
												<span v-if="isPending == 'binary_matrice'"><i class="fa fa-circle-o-notch fa-spin"> </i> Loading</span>
												<span v-if="isPending != 'binary_matrice'"><i class="fa fa-table"></i> Bentuk Binary Matriks</span>
											</button>
										</td>
									</tr>
									<tr>
										<td>Tabel probabilitas ditemukan</td>
										<td>
											<button @click="train_bayes" class="btn btn-success" :disabled="disabled == true">
												<span v-if="isPending == 'train_bayes'"><i class="fa fa-circle-o-notch fa-spin"> </i> Loading</span>
												<span v-if="isPending != 'train_bayes'"><i class="fa fa-align-left"></i> Train Bayes</span>
											</button>
										</td>
									</tr>								
									</table>
								</div>
							</div>
						</div>

					</div>

					<div class="row">
						<div class="col-6">
							<div class="card">
								<div class="card-header">
									<h6>Ekstraksi Kata Kunci</h6>
								</div>
								<div class="card-body">
									<table class="table table-bordered">	
									<tr>
										<td>Kata kunci ditemukan</td>
										<td>
											<button @click="ekstrak_kk" class="btn btn-primary" :disabled="disabled == true">
												<span v-if="isPending == 'ekstrak_kk'"><i class="fa fa-circle-o-notch fa-spin"> </i> Loading</span>
												<span  v-if="isPending != 'ekstrak_kk'"><i class="fa fa-font"></i> Ekstrak kata kunci</span>
											</button>
										</td>
									</tr>								
									</table>
								</div>
							</div>
						</div>

						<div class="col-6">
							<div class="card">
								<div class="card-header">
									<h6>Algoritma Klasifikasi</h6>
								</div>
								<div class="card-body">
									<table class="table table-bordered">	
									<tr>
										<td>
											<select v-model="method" class="form-control">
											  <option value=0>Naive-Bayes</option>
											  <option value=1>Correlation-Meassurement (Manual)</option>
											  <option value=2>Correlation-Meassurement (Auto)</option>
											</select>
										</td>
										<td>
											<button @click="categorize" class="btn btn-success" :disabled="disabled == true">								
												<span v-if="isPending == 'categorize'"><i class="fa fa-circle-o-notch fa-spin"></i> Loading</span>
												<span v-if="isPending != 'categorize'"><i class="fa fa-pie-chart"></i> Klasifikasi</span>
											</button>

										</td>
									</tr>								
									</table>
								</div>
							</div>
						</div>

					</div>
					<!-- /.row -->
				</div><!-- /.container-fluid -->
			</section>
			<!-- /.content -->
			<modal-alert v-bind:messages="messages"></modal-alert>
    </div>
</template>
<script>
    export default {
    	data(){
    		return{
    			disabled:false,
    			pending:"",
    			messages:"",
    			method:0
    		}
    	},

    	methods:{ 

    		categorize(){    			
    			this.pending = "categorize";
    			if(this.method==0){
    				this.post_data("/api/complaints/categorize/bayes",{});
    			}else if(this.method==1){
    				this.post_data("/api/complaints/categorize/cc",{});
    			}else{
    				this.post_data("/api/complaints/categorize/cc?type=auto",{});
    			}

    		},

    		train_bayes(){
    			this.pending = "train_bayes";
    			this.post_data("/api/trainings/bayes",{'project_id':1});
    		},

    		ekstrak_kk(){
    			this.pending ="ekstrak_kk";
    			this.post_data("/api/trainings/extract-keywords",{'project_id':1});
    		},

    		generate_bin_matrice(){
    			this.pending ="binary_matrice";
    			this.post_data("/api/trainings/binary-matrice",{'project_id':1});
    		},

    		clean_data(){
    			this.pending ="clean_data";
    			this.post_data("/api/trainings/clean-data",{'project_id':1});
    		},

    		post_data(url,body){
    			this.disabled = true;
		    	axios.post(url,body)
			        .then(({data}) => {
				        this.pending = "";
				        this.disabled = false;
				        this.messages = data.message;
				        $('#modalAlert').modal('show');
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
					   this.pending = "";
					   this.disabled = false;
		        }); 
    		},
    		
    	},

    	computed: {

		    isPending(){
		      return this.pending;
		    },
		}	


    }
</script>
<style>
	.green{
		color: green;
	}


</style>