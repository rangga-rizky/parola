<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><i class="fa fa-book"></i> Daftar Pengaduan</h1>
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
					<div class="col-3">
						<div class="card">
								<select class="form-control"   @change="fetch" v-model="selectedCategory">
									<option value="0">Semua Kategori</option>
	                                <option v-for="category in categories" :key="category.id" v-bind:value="category.slug">
	                                    {{ category.category }}
	                                </option>
	                             </select>
						</div>						
					</div>
				</div>
				<div class="row">
					<div class="col-12">					
						<div class="card">
							<div class="card-header">
								<strong>Jumlah Data : </strong>{{meta.number_of_data}}<br>
								<strong>Data Terbaru : </strong>{{meta.latest_data}}<br><br>
								<button @click="crawl" type="button" class="btn btn-primary" :disabled="isPending">
									<span v-if="isPending"><i class="fa fa-circle-o-notch fa-spin"></i> Loading</span>
									<span v-if="!isPending"><i class="fa fa-download"></i> Ambil data Twitter </span>
								</button>
								 <router-link  :to="{ name: 'upload-csv'}" type="button" :disabled="isPending" class="btn btn-success">
									<span><i class="fa fa-file"></i> Upload CSV file </span>
								</router-link >
								<router-link  :to="{ name: 'complaints-create'}" type="button" :disabled="isPending" class="btn btn-success">
									<span><i class="fa fa-plus"></i> Tambah Data</span>
								</router-link >
							</div>
							<div class="card-body">
								<span v-if="isLoading" >
									<i class="fa fa-spinner fa-spin"></i> Loading
								</span>
								 <div class="table-responsive" style="overflow: auto">
								<table v-if="!isLoading" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th>id</th>
											<th>Data</th>
											<th>predicted</th>
											<th>Date</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(complaint) in complaints"  v-bind:key="complaint.id" >
											<td>{{ complaint.id }}</td>
											<td>{{ complaint.tweet }}</td>
											<td>{{ complaint.predicted }}</td>
											<td>{{ complaint.date }}</td>
										</tr>										
									</tbody>
									<tfoot>
									</tfoot>								
								</table>
								 </div>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">								
								<pagination v-bind:pagination="paginator"  @paginate="fetch()" ></pagination>
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
    	 data() {
		    return {
		      complaints:[],
		      paginator: {},
		      messages:"",
		      meta:{},
		      loading:true,	
		      pending:false,
			   selectedCategory:"0",
		      categories:[],		      
		    };
		  },

		  created() {
		    this.fetch();			
		  	this.fetchCategory();
		  },

		  methods: {
		    fetch() {
				let categoryParam = "";
		    	if(this.selectedCategory != "0"){
		    		categoryParam = "&category_id="+this.selectedCategory;
		    	}
		    	axios.get('/api/complaints?page='+this.paginator.current_page+categoryParam)
			        .then(({data}) => {
				        this.complaints = data.data;
				        this.meta = data.meta;
				        this.paginator = data.paginator;
				        this.loading = false;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
					   this.loading = false;
		        });  
		    },

			fetchCategory() {
		    	axios.get('/api/categories')
			        .then(({data}) => {
				        this.categories = data;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan saat mengambil data kategori pada server");
		        });  
			},

		    crawl(){
		    	this.pending = true;
		    	axios.post('/api/tweets/crawl/LAPOR1708')
			        .then(({data}) => {
				        this.pending = false;
				        this.messages = data.messages;
				        $('#modalAlert').modal('show');
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
					   this.pending = false;
		        }); 
		    }


		  
		},

		computed: {
		    isLoading(){
		      return this.loading;
		    },

		    isPending(){
		      return this.pending;
		    }
		}	


    }
</script>