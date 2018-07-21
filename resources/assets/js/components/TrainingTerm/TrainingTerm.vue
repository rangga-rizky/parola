<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Daftar Kata Kunci Hasil Aggregasi</h1>
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
	                                <option v-for="category in categories" :key="category.id" v-bind:value="category.id">
	                                    {{ category.category }}
	                                </option>
	                             </select>
						</div>						
					</div>
				</div>
				<div class="row">
					<div class="col-10">					
						<div class="card">
							<div class="card-header">								
								 <text-field 
								 	name="term"
									placeholder="Cari Kata Kunci..."
									label=""
									info=""
									type="text"
									@changed="changeTerm"
									/>
								 <router-link  :to="{ name: 'training-terms-create' }"  class="btn btn-success">
									<i class="fa fa-plus"></i> Tambah Kata Kunci
								 </router-link>
								 <button @click="generateTermAssoc" type="button" class="btn btn-primary" :disabled="pending">
									 	<i :class="{'fa fa-table':!isPending, 'fa fa-circle-o-notch fa-spin':isPending}"></i> Bentuk matriks Asosiasi
								 </button>
							</div>
							<div class="card-body">
								<span v-if="isLoading" >
									<i class="fa fa-spinner fa-spin"></i> Loading
								</span>
								<table v-if="!isLoading" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th>id</th>
											<th>Kata</th>
											<th>Kategori</th>
											<th>Score</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(term, index) in terms"  v-bind:key="term.id" >
											<td>{{ term.id }}</td>
											<td>{{ term.term }}</td>
											<td>{{ term.category }}</td>
											<td>{{ term.score }}</td>
											<td>
												<div class="btn-group" role="group">
  												<router-link  :to="{ name: 'training-terms-edit', params: {id:term.id}}" type="button" class="btn btn-primary">
														  <i class="fa fa-edit"></i>
  												</router-link>
													<button @click="destroy(term.id,index)" type="button" class="btn btn-danger" :disabled="pending == true">
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
		      selectedCategory:"0",
			  terms:[],			  
			  messages:"",
		      categories:[],
		      paginator: {},
			  loading:true,	
			  pending:false,	      
		    };
		  },

		  created() {
		  	this.fetchCategory();
		    this.fetch();
		  },

		  methods: {
		    fetch() {
		    	let categoryParam = "";
		    	if(this.selectedCategory != "0"){
		    		categoryParam = "&category_id="+this.selectedCategory;
		    	}
		    	axios.get('/api/training-terms?page='+this.paginator.current_page+categoryParam)
			        .then(({data}) => {
				        this.terms = data.data;
				        this.paginator = data.paginator;
				        this.loading = false;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
					   this.loading = false;
		        });  
			},

			changeTerm(text){		
				if(text.length > 2){							
					this.loading = true;
					axios.get(`/api/training-terms/search/${text}`)
			        .then(({data}) => {
				        this.terms = data.data;
				        this.loading = false;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
					   this.loading = false;
		        	}); 
				}else if(text.length == 0){							
					this.loading = true;
					this.fetch();
				}
			},
			
			destroy(id,index) {
				if (confirm("Apakah Anda Yakin ?")) {				    
		    		this.pending = true;
		    		axios.delete('/api/training-terms/'+id)
			        .then(({data}) => {
			        	if(data.error == false){
                           this.$delete(this.terms, index);                                       
	                    }else{
	                        alert(data.message);
	                    }
			        	this.pending = false;
				    })
			        .catch((error) => {	     
					    if(error.response.status == 404){
							alert("Data tidak ditemukan");
						}else{
							console.log(error);
							//alert("Terjadi Kesalahan pada server");
						} 
						this.pending = false;
		        });  
				}
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
			
				generateTermAssoc() {
				this.pending = true;
		    		axios.post('api/training-terms/generate-assoc',{
						project_id:1
					})
			        .then(({data}) => {
						this.messages = "Matrik asosiasi berhasil dibuat";
						this.pending = false;
						 $('#modalAlert').modal('show');
				    })
			        .catch((error) => {	     
					    if(error.response.status == 404){
							alert("Data tidak ditemukan");
						}else{
							alert("Terjadi Kesalahan pada server");
						} 
						this.pending = false;
		        });  
		    },

		  
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