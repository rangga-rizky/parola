<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><i class="fa fa-list-alt"></i> Daftar Kategori</h1>
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
					<div class="col-8">
						<div class="card">
							<div class="card-header">
								 <router-link  :to="{ name: 'categories-create' }"  class="btn btn-success">
									<i class="fa fa-plus"></i> Tambah Kategori
								 </router-link>
							</div>
							<div class="card-body">
								<span v-if="isLoading" >
									<i class="fa fa-spinner fa-spin"></i> Loading
								</span>
								<table v-if="!isLoading" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th>id</th>
											<th>Kategori</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(category,index) in categories"  v-bind:key="category.id" >
											<td>{{ category.id }}</td>
											<td>{{ category.category }}</td>
											<td>
												<div class="btn-group" role="group">
  												<router-link  :to="{ name: 'categories-edit', params: {id:category.id}}" type="button" class="btn btn-primary">
														  <i class="fa fa-edit"></i>
  												</router-link>
													<button @click="destroy(category.id,index)" type="button" class="btn btn-danger" :disabled="pending == true">
															<i :class="{'fa fa-trash':!isPending, 'fa fa-circle-o-notch fa-spin':isPending}"></i>
													</button>
												</div>
											</td>
										</tr>										
									</tbody>
									<tfoot>
									</tfoot>
								
								</table>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">								
								
							</div>
						</div>
					</div>
				</div>
				<!-- /.row -->
			</div><!-- /.container-fluid -->
		</section>
		<!-- /.content -->
	</div>
</template>
<script>
    export default {
    	 data() {
		    return {
		      categories:[],
			  loading:true,	
			  pending:false	      
		    };
		  },

		  created() {
		    this.fetch();
		  },

		  methods: {
		    fetch() {
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
			
			destroy(id,index) {
				if (confirm("Apakah Anda Yakin ?")) {				    
		    		this.pending = true;
		    		axios.delete('/api/categories/'+id)
			        .then(({data}) => {
			        	if(data.error == false){
                           this.$delete(this.categories, index);                                       
	                    }else{
	                        alert(data.message);
	                    }
			        	this.pending = false;
				    })
			        .catch((error) => {	     
					    if(error.response.status == 404){
							alert("Data tidak ditemukan");
						}else{
							alert("Terjadi Kesalahan pada server");
						} 
						this.pending = false;
		        });  
				}
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